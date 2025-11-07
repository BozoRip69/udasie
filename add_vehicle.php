<?php
require 'config.php';
$user = require_login($db);
$pageTitle = "Dodaj pojazd po VIN";
include 'includes/header.php';

$message = "";
$vehicleData = null;
$debugJson = null;

// Ustawienia VINCARIO
$apiPrefix = "https://api.vincario.com/3.2";
$apiKey = "4e2c8f8b0302"; // Twój klucz
$secretKey = "f0af9472e3"; // Twój secret
$id = "decode";

// Krok 1: pobierz po VIN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vin']) && !isset($_POST['save'])) {
    $vin = mb_strtoupper(trim($_POST['vin']));
    if (strlen($vin) !== 17) {
        $message = "❌ Numer VIN musi mieć dokładnie 17 znaków.";
    } else {
        $controlSum = substr(sha1("$vin|$id|$apiKey|$secretKey"), 0, 10);
        $url = "$apiPrefix/$apiKey/$controlSum/decode/$vin.json";
        $data = @file_get_contents($url);
        if ($data === false) {
            $message = "❌ Błąd połączenia z VINCARIO.";
        } else {
            $result = json_decode($data, true);
            $debugJson = json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            if (!$result || isset($result['error'])) {
                $message = "❌ Brak danych z API.";
            } else {
                // mapuj decode[] by label => value
                $decoded = $result['decode'] ?? [];
                $map = [];
                foreach ($decoded as $item) {
                    if (isset($item['label'], $item['value'])) $map[$item['label']] = $item['value'];
                }

                $vehicleData = [
                    'vin' => $vin,
                    'brand' => $map['Make'] ?? '',
                    'model' => $map['Model'] ?? '',
                    'year' => $map['Model Year'] ?? '',
                    'fuel_type' => $map['Fuel Type - Primary'] ?? '',
                    'engine_capacity' => $map['Engine Displacement (ccm)'] ?? '',
                    'power' => $map['Engine Power (kW)'] ?? ($map['Max Speed (km/h)'] ?? ''), // próbujemy kilka etykiet
                    'transmission' => $map['Drive'] ?? '',
                    'country' => $map['Plant Country'] ?? '',
                    // pola dodatkowe, które API raczej nie daje -> zostaw puste do ręcznego wpisu
                    'registration_number' => '',
                    'mileage' => '',
                    'purchase_date' => '',
                    'inspection_date' => '',
                    'image' => ''
                ];
            }
        }
    }
}

// Krok 2: zapis (po wypełnieniu formularza edycji)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    // obsługa uploadu zdjęcia
    $imagePath = null;
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/vehicles/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
        $filename = time() . "_" . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', basename($_FILES["image"]["name"]));
        $targetFile = $targetDir . $filename;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $imagePath = $targetFile;
        }
    }

    // wstaw do bazy (używamy prepared PDO)
    $stmt = $db->prepare("INSERT INTO vehicles 
        (user_id, vin, brand, model, engine_type, year, fuel_type, engine_capacity, transmission, power, country, registration_number, mileage, purchase_date, inspection_date, image)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $user['id'],
        $_POST['vin'] ?: null,
        $_POST['brand'] ?: null,
        $_POST['model'] ?: null,
        $_POST['engine_type'] ?: null,
        $_POST['year'] ?: null,
        $_POST['fuel_type'] ?: null,
        $_POST['engine_capacity'] ?: null,
        $_POST['transmission'] ?: null,
        $_POST['power'] ?: null,
        $_POST['country'] ?: null,
        $_POST['registration_number'] ?: null,
        is_numeric($_POST['mileage']) ? (int)$_POST['mileage'] : 0,
        $_POST['purchase_date'] ?: null,
        $_POST['inspection_date'] ?: null,
        $imagePath
    ]);

    header("Location: garage.php?added=1");
    exit;
}
?>

<section class="dashboard">
  <h1><i class="fa-solid fa-car"></i> Dodaj pojazd po VIN</h1>

  <?php if ($message): ?>
    <div class="alert"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <?php if (!$vehicleData): ?>
    <form method="POST" class="vin-form">
      <label for="vin">Wprowadź numer VIN:</label>
      <input type="text" name="vin" id="vin" maxlength="17" required>
      <button type="submit" class="btn-small"><i class="fa-solid fa-search"></i> Sprawdź VIN</button>
    </form>
  <?php else: ?>
    <form method="POST" enctype="multipart/form-data" class="card edit-form">
      <h2>Podgląd i uzupełnienie danych przed zapisem</h2>

      <label>VIN</label>
      <input type="text" name="vin" value="<?= htmlspecialchars($vehicleData['vin']) ?>" readonly>

      <label>Marka</label>
      <input type="text" name="brand" value="<?= htmlspecialchars($vehicleData['brand']) ?>">

      <label>Model</label>
      <input type="text" name="model" value="<?= htmlspecialchars($vehicleData['model']) ?>">

      <label>Rok</label>
      <input type="number" name="year" value="<?= htmlspecialchars($vehicleData['year']) ?>">

      <label>Paliwo</label>
      <input type="text" name="fuel_type" value="<?= htmlspecialchars($vehicleData['fuel_type']) ?>">

      <label>Pojemność (ccm)</label>
      <input type="text" name="engine_capacity" value="<?= htmlspecialchars($vehicleData['engine_capacity']) ?>">

      <label>Moc (KM)</label>
      <input type="text" name="power" value="<?= htmlspecialchars($vehicleData['power']) ?>">

      <label>Skrzynia/Napęd</label>
      <input type="text" name="transmission" value="<?= htmlspecialchars($vehicleData['transmission']) ?>">

      <label>Kraj produkcji</label>
      <input type="text" name="country" value="<?= htmlspecialchars($vehicleData['country']) ?>">

      <hr>

      <label>Numer rejestracyjny (jeśli znasz)</label>
      <input type="text" name="registration_number" value="<?= htmlspecialchars($vehicleData['registration_number']) ?>">

      <label>Przebieg (km)</label>
      <input type="number" name="mileage" min="0" value="<?= htmlspecialchars($vehicleData['mileage']) ?>">

      <label>Data zakupu</label>
      <input type="date" name="purchase_date" value="<?= htmlspecialchars($vehicleData['purchase_date']) ?>">

      <label>Data przeglądu</label>
      <input type="date" name="inspection_date" value="<?= htmlspecialchars($vehicleData['inspection_date']) ?>">

      <label>Zdjęcie (opcjonalnie)</label>
      <input type="file" name="image" accept="image/*">

      <input type="hidden" name="save" value="1">
      <button type="submit" class="btn-save">💾 Zapisz pojazd</button>
      <a href="garage.php" class="btn-cancel">Anuluj</a>
    </form>

    <?php if ($debugJson): ?>
      <div class="debug-box">
        <h3>🔍 Odpowiedź API VINCARIO (Debug JSON)</h3>
        <pre><?= htmlspecialchars($debugJson) ?></pre>
        <button type="button" class="btn-small" onclick="copyDebug()">📋 Skopiuj JSON</button>
      </div>
    <?php endif; ?>
  <?php endif; ?>
</section>

<style>
/* proste style, dopasuj do header/footer */
.dashboard { max-width:800px; margin:30px auto; }
.vin-form input { padding:10px; width:320px; margin-right:10px; border-radius:6px; border:1px solid #ccc; }
.card.edit-form { background:#fff; padding:16px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.06); }
.edit-form label { display:block; font-weight:600; margin-top:10px; }
.edit-form input { width:100%; padding:8px; margin-top:6px; border-radius:6px; border:1px solid #ddd; }
.btn-save { background:#28a745; color:#fff; padding:10px 14px; border-radius:8px; border:none; margin-top:12px; }
.btn-cancel { margin-left:12px; color:#555; text-decoration:none; }
.debug-box { margin-top:16px; background:#111; color:#cfefff; padding:12px; border-radius:8px; font-size:13px; overflow:auto; }
</style>

<script>
function copyDebug() {
  const pre = document.querySelector('.debug-box pre');
  if (!pre) return;
  navigator.clipboard.writeText(pre.innerText).then(()=> alert('✅ Skopiowano JSON'), ()=> alert('❌ Nie udało się skopiować'));
}
</script>

<?php include 'includes/footer.php'; ?>
