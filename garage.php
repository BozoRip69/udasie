<?php
require 'config.php';
$user = require_login($db);
$pageTitle = "Twój garaż";
include 'includes/header.php';

// Pobierz auta użytkownika
$stmt = $db->prepare("SELECT * FROM cars WHERE user_id = ?");
$stmt->execute([$user['id']]);
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="garage">
  <h1>Twój garaż</h1>
  <form action="car_action.php" method="post" enctype="multipart/form-data" class="card">
    <h2>Dodaj samochód</h2>
    <label>Marka</label>
    <input type="text" name="brand" required>
    <label>Model</label>
    <input type="text" name="model" required>
    <label>Rok</label>
    <input type="number" name="year" min="1900" max="<?= date('Y') ?>" required>
    <label>Przebieg (km)</label>
    <input type="number" name="mileage" min="0" required>
    <label>Zdjęcie (opcjonalne)</label>
    <input type="file" name="image" accept="image/*">
    <button type="submit" name="action" value="add">Dodaj</button>
  </form>

  <div class="garage-list">
    <?php if ($cars): ?>
      <?php foreach ($cars as $car): ?>
        <div class="car-card card">
          <img src="<?= $car['image'] ?: 'uploads/avatars/default-car.png' ?>" alt="Samochód">
          <h3><?= htmlspecialchars($car['brand'].' '.$car['model']) ?></h3>
          <p>Rok: <?= $car['year'] ?></p>
          <p>Przebieg: <?= number_format($car['mileage']) ?> km</p>
          <form method="post" action="car_action.php">
            <input type="hidden" name="id" value="<?= $car['id'] ?>">
            <button type="submit" name="action" value="delete" class="danger">Usuń</button>
          </form>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Nie masz jeszcze żadnych samochodów.</p>
    <?php endif; ?>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
