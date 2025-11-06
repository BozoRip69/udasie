<?php
require 'config.php';
$user = require_login($db);
$pageTitle = "Akumulatory";
include 'includes/header.php';

// Pobierz auta i akumulatory użytkownika
$cars = $db->prepare("SELECT id, brand, model FROM cars WHERE user_id = ?");
$cars->execute([$user['id']]);
$carList = $cars->fetchAll(PDO::FETCH_ASSOC);

$stmt = $db->prepare("
  SELECT b.*, c.brand, c.model 
  FROM batteries b 
  LEFT JOIN cars c ON c.id = b.car_id 
  WHERE b.user_id = ?");
$stmt->execute([$user['id']]);
$batteries = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="batteries">
  <h1>Twoje akumulatory</h1>
  <form action="battery_action.php" method="post" class="card">
    <h2>Dodaj akumulator</h2>
    <label>Marka</label>
    <input type="text" name="brand" required>
    <label>Pojemność (Ah)</label>
    <input type="text" name="capacity">
    <label>Napięcie (V)</label>
    <input type="text" name="voltage">
    <label>Przypisz do samochodu</label>
    <select name="car_id">
      <option value="">(brak)</option>
      <?php foreach ($carList as $car): ?>
        <option value="<?= $car['id'] ?>"><?= htmlspecialchars($car['brand'].' '.$car['model']) ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit" name="action" value="add">Dodaj</button>
  </form>

  <div class="garage-list">
    <?php if ($batteries): ?>
      <?php foreach ($batteries as $b): ?>
        <div class="car-card card">
          <h3><?= htmlspecialchars($b['brand']) ?></h3>
          <p>Pojemność: <?= htmlspecialchars($b['capacity']) ?> Ah</p>
          <p>Napięcie: <?= htmlspecialchars($b['voltage']) ?> V</p>
          <p>Samochód: <?= htmlspecialchars($b['brand'].' '.$b['model'] ?: 'brak') ?></p>
          <form method="post" action="battery_action.php">
            <input type="hidden" name="id" value="<?= $b['id'] ?>">
            <button type="submit" name="action" value="delete" class="danger">Usuń</button>
          </form>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Brak akumulatorów.</p>
    <?php endif; ?>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
