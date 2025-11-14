<?php
require 'config.php';
$user = require_login($db);
$pageTitle = "Moje akumulatory";
include 'includes/header.php';

// Pobierz samochody użytkownika
$stmt = $db->prepare("SELECT id, brand, model, vin FROM vehicles WHERE user_id = ?");
$stmt->execute([$user['id']]);
$vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pobierz akumulatory
$stmt = $db->prepare("
  SELECT ub.*, b.battery_model, b.battery_image, b.symkar, b.production_date,
         v.brand AS vehicle_brand, v.model AS vehicle_model
  FROM user_batteries ub
  JOIN batteries b ON ub.battery_id = b.id
  LEFT JOIN vehicles v ON ub.vehicle_id = v.id
  WHERE ub.user_id = ?
  ORDER BY ub.added_at DESC
");
$stmt->execute([$user['id']]);
$userBatteries = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="garage-container">

  <div class="section-header">
    <h1>🔋 Twoje akumulatory</h1>
    <a href="#addBattery" class="btn-primary">➕ Dodaj akumulator</a>
  </div>

  <!-- ========================= -->
  <!--      FORMULARZ DODAWANIA -->
  <!-- ========================= -->

  <form id="addBattery" action="user_battery_action.php" method="post" enctype="multipart/form-data" class="card form-card">

    <h2>Dodaj akumulator</h2>

    <label>Numer seryjny akumulatora</label>
    <div class="search-wrapper">
      <input type="text" id="serial_number" name="serial_number" placeholder="Wpisz numer seryjny..." autocomplete="off" required>
      <button type="button" class="search-btn" id="searchBattery">🔍</button>
    </div>

    <div id="batterySuggestions" class="suggestions"></div>

    <div id="batteryPreview" class="battery-preview" style="display:none;">
      <img id="batteryImage" src="" alt="Podgląd akumulatora">
      <div class="preview-meta">
        <p><strong>Model:</strong> <span id="batteryModel"></span></p>
        <p><strong>Symkar:</strong> <span id="batterySymkar"></span></p>
        <p><strong>Data produkcji:</strong> <span id="batteryProd"></span></p>
      </div>
    </div>

    <input type="hidden" id="battery_id" name="battery_id">

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

    <label>Zdjęcie paragonu</label>
    <input type="file" name="receipt_image" accept="image/*">

    <label>Uwagi (opcjonalne)</label>
    <textarea name="notes" rows="3" placeholder="np. wymiana po 3 latach..."></textarea>

    <button type="submit" name="action" value="add" class="btn-primary save-btn">💾 Zapisz</button>

  </form>

  <!-- ========================= -->
  <!--         LISTA KART        -->
  <!-- ========================= -->

  <div class="battery-list">

    <?php if ($userBatteries): ?>
      <?php foreach ($userBatteries as $b): ?>

        <div class="battery-card card">

          <div class="battery-left">
            <img src="<?= htmlspecialchars($b['battery_image'] ?: 'uploads/avatars/default-battery.png') ?>" class="battery-thumb">
          </div>

          <div class="battery-info">
            <h3><?= htmlspecialchars($b['battery_model']) ?></h3>
            <p><strong>Pojazd:</strong> <?= htmlspecialchars($b['vehicle_brand'].' '.$b['vehicle_model'] ?: '-') ?></p>
            <p><strong>Miejsce zakupu:</strong> <?= htmlspecialchars($b['purchase_place'] ?: '-') ?></p>
            <p><strong>Data zakupu:</strong> <?= htmlspecialchars($b['purchase_date'] ?: '-') ?></p>

            <?php if ($b['receipt_image']): ?>
              <a href="<?= htmlspecialchars($b['receipt_image']) ?>" target="_blank" class="btn-small">🧾 Paragon</a>
            <?php endif; ?>

            <?php if ($b['notes']): ?>
              <div class="battery-notes">
                <strong>Uwagi:</strong> <?= nl2br(htmlspecialchars($b['notes'])) ?>
              </div>
            <?php endif; ?>
          </div>

          <div class="battery-actions">
            <a href="user_battery_edit.php?id=<?= $b['id'] ?>" class="btn-edit">✏️ Edytuj</a>

            <form method="post" action="user_battery_action.php" onsubmit="return confirm('Na pewno usunąć ten akumulator?');">
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
<?php include 'assets/batteries.css'; ?>
</style>

<script src="assets/batteries.js"></script>

<?php include 'includes/footer.php'; ?>
