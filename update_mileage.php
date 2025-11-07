<?php
require 'config.php';
$user = require_login($db);

$vehicle_id = $_GET['vehicle_id'] ?? null;
if (!$vehicle_id) {
    header("Location: garage.php");
    exit;
}

// Sprawdź, czy pojazd należy do użytkownika
$stmt = $db->prepare("SELECT id, user_id FROM vehicles WHERE id = ?");
$stmt->execute([$vehicle_id]);
$vehicle = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vehicle || $vehicle['user_id'] != $user['id']) {
    header("Location: garage.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mileage = (int)$_POST['mileage'];
    if ($mileage > 0) {
        // ✅ Dodaj user_id, żeby nie złamać klucza obcego
        $stmt = $db->prepare("
          INSERT INTO vehicle_mileage (vehicle_id, user_id, mileage, recorded_at)
          VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$vehicle_id, $user['id'], $mileage]);

        // Zaktualizuj dane pojazdu i zresetuj alert
        $update = $db->prepare("UPDATE vehicles SET mileage = ?, needs_update = 0 WHERE id = ?");
        $update->execute([$mileage, $vehicle_id]);

        header("Location: garage.php?updated=1");
        exit;
    } else {
        $error = "❌ Podaj poprawny przebieg (większy niż 0).";
    }
}

$pageTitle = "Aktualizacja przebiegu";
include 'includes/header.php';
?>

<section class="mileage-update">
  <h1>🔧 Aktualizacja przebiegu</h1>
  <?php if (!empty($error)): ?><div class="alert-error"><?= $error ?></div><?php endif; ?>
  <form method="post" class="mileage-form">
    <label>Nowy przebieg (km)</label>
    <input type="number" name="mileage" required min="1" placeholder="np. 123456">
    <button type="submit" class="btn">💾 Zapisz</button>
  </form>
</section>

<style>
.mileage-update {
  max-width: 600px;
  margin: 60px auto;
  padding: 30px;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
body.dark .mileage-update {
  background: #161b22;
  color: #e5e7eb;
}
.mileage-form label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
}
.mileage-form input {
  width: 100%;
  padding: 10px;
  border-radius: 8px;
  border: 1px solid #ccc;
  margin-bottom: 15px;
}
body.dark .mileage-form input {
  background: #0f172a;
  border-color: #334155;
  color: #e5e7eb;
}
.btn {
  background: #007bff;
  color: white;
  border: none;
  padding: 10px 16px;
  border-radius: 8px;
  cursor: pointer;
}
.btn:hover { background: #0056b3; }
.alert-error {
  background: rgba(255,99,71,0.1);
  border: 1px solid #ff6961;
  color: #ff6961;
  padding: 8px;
  border-radius: 6px;
  margin-bottom: 10px;
}
</style>

<?php include 'includes/footer.php'; ?>
