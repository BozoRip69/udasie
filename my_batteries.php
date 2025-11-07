<?php
require 'config.php';
$user = require_login($db);
$pageTitle = "Moje akumulatory";
include 'includes/header.php';

// Pobierz samochody użytkownika
$stmt = $db->prepare("SELECT id, brand, model, vin FROM vehicles WHERE user_id = ?");
$stmt->execute([$user['id']]);
$vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pobierz wszystkie akumulatory z bazy (modele)
$batteryModels = $db->query("SELECT id, battery_model, serial_number FROM batteries ORDER BY battery_model ASC")->fetchAll(PDO::FETCH_ASSOC);

// Pobierz akumulatory przypisane do użytkownika
$stmt = $db->prepare("
  SELECT ub.*, b.battery_model, v.brand AS vehicle_brand, v.model AS vehicle_model
  FROM user_batteries ub
  JOIN batteries b ON ub.battery_id = b.id
  LEFT JOIN vehicles v ON ub.vehicle_id = v.id
  WHERE ub.user_id = ?
  ORDER BY ub.added_at DESC
");
$stmt->execute([$user['id']]);
$userBatteries = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="garage">
  <div class="garage-header">
    <h1>🔋 Twoje akumulatory</h1>
    <a href="#addBattery" class="btn-vin">➕ Dodaj akumulator</a>
  </div>

  <form id="addBattery" action="user_battery_action.php" method="post" enctype="multipart/form-data" class="card">
    <h2>Dodaj akumulator</h2>

    <label>Model akumulatora</label>
    <select name="battery_id" required>
      <option value="">— Wybierz model —</option>
      <?php foreach ($batteryModels as $bm): ?>
        <option value="<?= $bm['id'] ?>">
          <?= htmlspecialchars($bm['battery_model'] . ' (SN: ' . $bm['serial_number'] . ')') ?>
        </option>
      <?php endforeach; ?>
    </select>

    <label>Przypisz do pojazdu</label>
    <select name="vehicle_id">
      <option value="">— Brak —</option>
      <?php foreach ($vehicles as $v): ?>
        <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['brand'].' '.$v['model'].' ('.$v['vin'].')') ?></option>
      <?php endforeach; ?>
    </select>

    <label>Miejsce zakupu</label>
    <input type="text" name="purchase_place" placeholder="np. AutoLand Kraków">

    <label>Data zakupu</label>
    <input type="date" name="purchase_date">

    <label>Zdjęcie akumulatora</label>
    <input type="file" name="battery_image" accept="image/*">

    <label>Zdjęcie paragonu</label>
    <input type="file" name="receipt_image" accept="image/*">

    <label>Uwagi (opcjonalne)</label>
    <textarea name="notes" rows="3" placeholder="np. wymiana po 3 latach, działa dobrze..."></textarea>

    <button type="submit" name="action" value="add" class="btn">💾 Zapisz</button>
  </form>

  <hr style="margin:30px 0; border:0; border-top:1px solid #ddd;">

  <div class="garage-list">
    <?php if ($userBatteries): ?>
      <?php foreach ($userBatteries as $b): ?>
        <div class="car-card card">
          <div class="car-top">
            <div class="car-thumb-wrap">
              <img src="<?= htmlspecialchars($b['battery_image'] ?: 'uploads/avatars/default-battery.png') ?>" alt="Akumulator" class="car-thumb">
            </div>
            <div class="car-meta">
              <h3><?= htmlspecialchars($b['battery_model']) ?></h3>
              <p><strong>Pojazd:</strong> <?= htmlspecialchars($b['vehicle_brand'].' '.$b['vehicle_model'] ?: '-') ?></p>
              <p><strong>Miejsce zakupu:</strong> <?= htmlspecialchars($b['purchase_place'] ?: '-') ?></p>
              <p><strong>Data zakupu:</strong> <?= htmlspecialchars($b['purchase_date'] ?: '-') ?></p>
              <?php if ($b['receipt_image']): ?>
                <a href="<?= htmlspecialchars($b['receipt_image']) ?>" target="_blank" class="btn-small">🧾 Paragon</a>
              <?php endif; ?>
            </div>
          </div>
          <?php if ($b['notes']): ?>
            <div class="car-body">
              <p><strong>Uwagi:</strong> <?= htmlspecialchars($b['notes']) ?></p>
            </div>
          <?php endif; ?>
          <div class="car-actions">
            <a href="user_battery_edit.php?id=<?= $b['id'] ?>" class="btn-edit">✏️ Edytuj</a>
            <form method="post" action="user_battery_action.php" style="display:inline;">
              <input type="hidden" name="id" value="<?= $b['id'] ?>">
              <button type="submit" name="action" value="delete" class="danger">🗑 Usuń</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Nie masz jeszcze żadnych akumulatorów.</p>
    <?php endif; ?>
  </div>
</section>

<style>
<?php include 'assets/style.css'; ?>
</style>

<?php include 'includes/footer.php'; ?>
