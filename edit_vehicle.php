<?php
require 'config.php';
$user = require_login($db);
$pageTitle = "Edytuj pojazd";
include 'includes/header.php';

// Sprawdź, czy przekazano ID
if (!isset($_GET['id'])) {
    header("Location: garage.php");
    exit;
}

$id = (int)$_GET['id'];
$stmt = $db->prepare("SELECT * FROM vehicles WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $user['id']]);
$vehicle = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vehicle) {
    echo "<p>Pojazd nie został znaleziony lub nie masz do niego dostępu.</p>";
    include 'includes/footer.php';
    exit;
}

// Zapis zmian
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imagePath = $vehicle['image'];

    // Obsługa uploadu nowego zdjęcia
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/vehicles/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
        $filename = time() . "_" . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', basename($_FILES["image"]["name"]));
        $targetFile = $targetDir . $filename;

        // Walidacja pliku (max 3MB i tylko JPG/PNG/WebP)
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (in_array(mime_content_type($_FILES['image']['tmp_name']), $allowed) && $_FILES['image']['size'] <= 3*1024*1024) {
            move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);
            $imagePath = $targetFile;
        }
    }

    // Dane z formularza
    $fields = [
        'brand' => $_POST['brand'] ?? '',
        'model' => $_POST['model'] ?? '',
        'year' => $_POST['year'] ?? '',
        'vin' => $_POST['vin'] ?? '',
        'registration_number' => $_POST['registration_number'] ?? '',
        'mileage' => (int)($_POST['mileage'] ?? 0),
        'purchase_date' => $_POST['purchase_date'] ?: null,
        'inspection_date' => $_POST['inspection_date'] ?: null,
        'fuel_type' => $_POST['fuel_type'] ?? '',
        'engine_capacity' => $_POST['engine_capacity'] ?? '',
        'power' => $_POST['power'] ?? '',
        'transmission' => $_POST['transmission'] ?? '',
        'country' => $_POST['country'] ?? '',
    ];

    $stmt = $db->prepare("
        UPDATE vehicles SET
            brand = ?, model = ?, year = ?, vin = ?, registration_number = ?,
            mileage = ?, purchase_date = ?, inspection_date = ?,
            fuel_type = ?, engine_capacity = ?, power = ?, transmission = ?, country = ?, image = ?
        WHERE id = ? AND user_id = ?
    ");
    $stmt->execute([
        $fields['brand'], $fields['model'], $fields['year'], $fields['vin'], $fields['registration_number'],
        $fields['mileage'], $fields['purchase_date'], $fields['inspection_date'],
        $fields['fuel_type'], $fields['engine_capacity'], $fields['power'], $fields['transmission'], $fields['country'],
        $imagePath, $id, $user['id']
    ]);

    header("Location: garage.php?updated=1");
    exit;
}
?>

<section class="edit-vehicle">
  <h1>✏️ Edytuj pojazd</h1>

  <form method="post" enctype="multipart/form-data" class="edit-form card">
    <div class="image-preview">
      <img src="<?= htmlspecialchars($vehicle['image'] ?: 'uploads/avatars/default-car.png') ?>" alt="Samochód">
    </div>

    <label>Marka</label>
    <input type="text" name="brand" value="<?= htmlspecialchars($vehicle['brand'] ?? '') ?>">

    <label>Model</label>
    <input type="text" name="model" value="<?= htmlspecialchars($vehicle['model'] ?? '') ?>">

    <label>Rok</label>
    <input type="number" name="year" min="1900" max="<?= date('Y') + 1 ?>" value="<?= htmlspecialchars($vehicle['year'] ?? '') ?>">

    <label>VIN</label>
    <input type="text" name="vin" maxlength="17" value="<?= htmlspecialchars($vehicle['vin'] ?? '') ?>">

    <label>Numer rejestracyjny</label>
    <input type="text" name="registration_number" maxlength="20" value="<?= htmlspecialchars($vehicle['registration_number'] ?? '') ?>">

    <label>Przebieg (km)</label>
    <input type="number" name="mileage" min="0" value="<?= htmlspecialchars($vehicle['mileage'] ?? 0) ?>">

    <label>Data zakupu</label>
    <input type="date" name="purchase_date" value="<?= htmlspecialchars($vehicle['purchase_date'] ?? '') ?>">

    <label>Data przeglądu</label>
    <input type="date" name="inspection_date" value="<?= htmlspecialchars($vehicle['inspection_date'] ?? '') ?>">

    <label>Paliwo</label>
    <input type="text" name="fuel_type" value="<?= htmlspecialchars($vehicle['fuel_type'] ?? '') ?>">

    <label>Pojemność (ccm)</label>
    <input type="text" name="engine_capacity" value="<?= htmlspecialchars($vehicle['engine_capacity'] ?? '') ?>">

    <label>Moc (KM)</label>
    <input type="text" name="power" value="<?= htmlspecialchars($vehicle['power'] ?? '') ?>">

    <label>Skrzynia / Napęd</label>
    <input type="text" name="transmission" value="<?= htmlspecialchars($vehicle['transmission'] ?? '') ?>">

    <label>Kraj produkcji</label>
    <input type="text" name="country" value="<?= htmlspecialchars($vehicle['country'] ?? '') ?>">

    <label>Nowe zdjęcie (opcjonalnie)</label>
    <input type="file" name="image" accept="image/*">

    <div class="actions">
      <button type="submit" class="btn-save"><i class="fa-solid fa-save"></i> Zapisz zmiany</button>
      <a href="garage.php" class="btn-cancel">Anuluj</a>
    </div>
  </form>
</section>
<?php include 'includes/footer.php'; ?>
