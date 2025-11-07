<?php
require 'config.php';
$user = require_login($db);
$pageTitle = "Edytuj akumulator";
include 'includes/header.php';

if (empty($_GET['id'])) {
  header("Location: my_batteries.php");
  exit;
}

$id = (int) $_GET['id'];

// Pobierz dane akumulatora użytkownika
$stmt = $db->prepare("
  SELECT ub.*, b.battery_model, b.serial_number, v.brand AS vehicle_brand, v.model AS vehicle_model
  FROM user_batteries ub
  JOIN batteries b ON ub.battery_id = b.id
  LEFT JOIN vehicles v ON ub.vehicle_id = v.id
  WHERE ub.id = ? AND ub.user_id = ?
");
$stmt->execute([$id, $user['id']]);
$battery = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$battery) {
  echo "<p>Nie znaleziono akumulatora.</p>";
  include 'includes/footer.php';
  exit;
}

// Pobierz wszystkie pojazdy użytkownika
$stmt = $db->prepare("SELECT id, brand, model, vin FROM vehicles WHERE user_id = ?");
$stmt->execute([$user['id']]);
$vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="garage">
  <div class="garage-header">
    <h1>✏️ Edytuj akumulator</h1>
    <a href="my_batteries.php" class="btn-vin">← Wróć</a>
  </div>

  <form action="user_battery_action.php" method="post" enctype="multipart/form-data" class="card">
    <input type="hidden" name="action" value="edit">
    <input type="hidden" name="id" value="<?= htmlspecialchars($battery['id']) ?>">

    <h2><?= htmlspecialchars($battery['battery_model']) ?> (SN: <?= htmlspecialchars($battery['serial_number']) ?>)</h2>

    <label>Przypisany pojazd</label>
    <select name="vehicle_id">
      <option value="">— Brak —</option>
      <?php foreach ($vehicles as $v): ?>
        <option value="<?= $v['id'] ?>" <?= $battery['vehicle_id'] == $v['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($v['brand'].' '.$v['model'].' ('.$v['vin'].')') ?>
        </option>
      <?php endforeach; ?>
    </select>

    <label>Miejsce zakupu</label>
    <input type="text" name="purchase_place" value="<?= htmlspecialchars($battery['purchase_place']) ?>" placeholder="np. AutoLand Kraków">

    <label>Data zakupu</label>
    <input type="date" name="purchase_date" value="<?= htmlspecialchars($battery['purchase_date']) ?>">

    <div class="image-section">
      <label>Zdjęcie akumulatora</label><br>
      <?php if (!empty($battery['battery_image'])): ?>
        <img src="<?= htmlspecialchars($battery['battery_image']) ?>" alt="Akumulator" class="car-thumb" style="max-width:160px; margin-bottom:10px;">
      <?php endif; ?>
      <input type="file" name="battery_image" accept="image/*">
    </div>

    <div class="image-section">
      <label>Zdjęcie paragonu</label><br>
      <?php if (!empty($battery['receipt_image'])): ?>
        <a href="<?= htmlspecialchars($battery['receipt_image']) ?>" target="_blank">
          <img src="<?= htmlspecialchars($battery['receipt_image']) ?>" alt="Paragon" class="car-thumb" style="max-width:160px; margin-bottom:10px;">
        </a>
      <?php endif; ?>
      <input type="file" name="receipt_image" accept="image/*">
    </div>

    <label>Uwagi</label>
    <textarea name="notes" rows="3"><?= htmlspecialchars($battery['notes']) ?></textarea>

    <button type="submit" class="btn">💾 Zapisz zmiany</button>
  </form>
</section>

<style>
<?php include 'assets/css/garage.css'; ?>
.image-section img {
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  border: 1px solid #ccc;
}
body.dark .image-section img {
  border-color: #2c2f33;
  box-shadow: 0 4px 12px rgba(255,255,255,0.05);
}
</style>

<?php include 'includes/footer.php'; ?>
