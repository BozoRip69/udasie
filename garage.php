<?php
require 'config.php';
$user = require_login($db);
$pageTitle = "Twój garaż";
include 'includes/header.php';

$stmt = $db->prepare("SELECT * FROM vehicles WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user['id']]);
$vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalMileage = 0;
foreach ($vehicles as $v) {
    $totalMileage += (int)($v['mileage'] ?? 0);
}

$notice = '';
if (isset($_GET['updated'])) $notice = '✅ Dane pojazdu zostały zaktualizowane.';
if (isset($_GET['deleted'])) $notice = '✅ Pojazd został usunięty.';
if (isset($_GET['added'])) $notice = '✅ Pojazd został dodany.';
?>

<section class="garage">
  <div class="garage-header">
    <h1>🚗 Twój garaż</h1>
    <div class="garage-actions">
      <a href="add_vehicle.php" class="btn-vin"><i class="fa-solid fa-car"></i> Dodaj pojazd po VIN</a>
    </div>
  </div>

  <?php if ($notice): ?>
    <div class="notice"><?= htmlspecialchars($notice) ?></div>
  <?php endif; ?>

  <div class="garage-summary">
    <h3>📊 Łączny przebieg wszystkich pojazdów: 
      <span class="highlight"><?= number_format($totalMileage, 0, ',', ' ') ?> km</span>
    </h3>
  </div>

  <div class="garage-list">
    <?php if ($vehicles): ?>
      <?php foreach ($vehicles as $v): ?>
        <div class="car-card card">
          <div class="car-top">
            <div class="car-thumb-wrap">
              <img class="car-thumb" src="<?= htmlspecialchars($v['image'] ?: 'uploads/avatars/default-car.png') ?>" alt="<?= htmlspecialchars($v['brand'].' '.$v['model']) ?>">
            </div>

            <div class="car-meta">
              <h3><?= htmlspecialchars($v['brand'] . ' ' . $v['model']) ?></h3>
              <p class="vin">VIN: <?= htmlspecialchars($v['vin'] ?: '-') ?></p>
              <p><strong>Rok:</strong> <?= htmlspecialchars($v['year'] ?: '-') ?></p>
              <p><strong>Rejestracja:</strong> <?= htmlspecialchars($v['registration_number'] ?: '-') ?></p>
            </div>
          </div>

          <div class="car-body">
            <ul class="car-info">
              <li><strong>Przebieg:</strong> <?= number_format((int)($v['mileage'] ?? 0), 0, ',', ' ') ?> km</li>
              <li><strong>Zakup:</strong> <?= htmlspecialchars($v['purchase_date'] ?: '-') ?></li>
              <li><strong>Przegląd:</strong> <?= htmlspecialchars($v['inspection_date'] ?: '-') ?></li>
              <li><strong>Paliwo:</strong> <?= htmlspecialchars($v['fuel_type'] ?: '-') ?></li>
              <li><strong>Pojemność:</strong> <?= htmlspecialchars($v['engine_capacity'] ?: '-') ?> <?= $v['engine_capacity'] ? 'ccm' : '' ?></li>
              <li><strong>Moc:</strong> <?= htmlspecialchars($v['power'] ?: '-') ?> <?= $v['power'] ? 'KM' : '' ?></li>
              <li><strong>Skrzynia:</strong> <?= htmlspecialchars($v['transmission'] ?: '-') ?></li>
              <li><strong>Kraj:</strong> <?= htmlspecialchars($v['country'] ?: '-') ?></li>
            </ul>
          </div>

          <div class="car-actions">
            <a href="edit_vehicle.php?id=<?= (int)$v['id'] ?>" class="btn-edit"><i class="fa-solid fa-pen"></i> Edytuj</a>

            <form method="post" action="vehicle_action.php" style="display:inline-block;">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= (int)$v['id'] ?>">
              <button type="submit" class="danger" onclick="return confirm('Czy na pewno usunąć pojazd?');">
                <i class="fa-solid fa-trash"></i> Usuń
              </button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Nie masz jeszcze żadnych pojazdów w garażu.</p>
    <?php endif; ?>
  </div>
</section>

<style>
.garage {
  max-width: 1100px;
  margin: 40px auto;
  padding: 0 20px;
  font-family: 'Inter', sans-serif;
}

.garage-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.garage-header h1 {
  font-size: 1.9rem;
  font-weight: 700;
  color: var(--primary-dark, #073b66);
  display: flex;
  align-items: center;
  gap: 10px;
}

body.dark .garage-header h1 {
  color: #60a5fa;
}

.garage-actions .btn-vin {
  background: var(--accent, #007bff);
  color: #fff;
  padding: 10px 18px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  box-shadow: 0 2px 6px rgba(0, 123, 255, 0.3);
  transition: 0.2s;
}

.garage-actions .btn-vin:hover {
  background: var(--primary, #0056b3);
  box-shadow: 0 4px 10px rgba(0, 123, 255, 0.4);
}

.notice {
  background: #e9f7ef;
  border: 1px solid #c7eed2;
  color: #155724;
  padding: 10px;
  border-radius: 8px;
  margin-bottom: 12px;
}

body.dark .notice {
  background: rgba(46, 204, 113, 0.1);
  border-color: #16a34a;
  color: #4ade80;
}

.garage-summary {
  margin-bottom: 20px;
  text-align: center;
  color: var(--primary-dark, #073b66);
  font-size: 1.05rem;
}

.garage-summary .highlight {
  color: #28a745;
  font-weight: bold;
}

body.dark .garage-summary {
  color: #e5e7eb;
}

body.dark .garage-summary .highlight {
  color: #4ade80;
}

.garage-list {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
  gap: 20px;
}

.car-card {
  background: #ffffff;
  border-radius: 12px;
  padding: 16px;
  box-shadow: 0 6px 18px rgba(17, 24, 39, 0.06);
  display: flex;
  flex-direction: column;
  gap: 12px;
  border: 1px solid #f1f5f9;
  transition: all 0.25s ease;
}

.car-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(0, 123, 255, 0.15);
}

body.dark .car-card {
  background: #161b22;
  border-color: #2c2f33;
  box-shadow: none;
}

body.dark .car-card:hover {
  box-shadow: 0 0 12px rgba(0, 120, 255, 0.25);
}

.car-top {
  display: flex;
  gap: 16px;
  align-items: flex-start;
}

.car-thumb-wrap {
  width: 140px;
  flex: 0 0 140px;
}

.car-thumb {
  width: 140px;
  height: 140px;
  object-fit: cover;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.car-thumb:hover {
  transform: scale(1.05);
  box-shadow: 0 6px 18px rgba(0,0,0,0.2);
}

body.dark .car-thumb {
  border-color: #2c2f33;
  box-shadow: 0 4px 10px rgba(255,255,255,0.05);
}

.car-meta h3 {
  margin: 0 0 6px 0;
  font-size: 1.15rem;
  color: var(--primary, #0046ad);
}

.car-meta p {
  margin: 0;
  color: #555;
  font-size: 0.95rem;
}

body.dark .car-meta h3 {
  color: #3b82f6;
}

body.dark .car-meta p {
  color: #e5e7eb;
}

.car-body {
  border-top: 1px solid #f1f5f9;
  padding-top: 12px;
}

body.dark .car-body {
  border-color: #2c2f33;
}

.car-info {
  list-style: none;
  padding: 0;
  margin: 0;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 6px 20px;
  font-size: 0.95rem;
  color: #444;
}

.car-info li {
  padding-bottom: 6px;
  border-bottom: 1px solid #fafafa;
}

body.dark .car-info {
  color: #e5e7eb;
}

body.dark .car-info li {
  border-color: rgba(255,255,255,0.08);
}

.car-actions {
  display: flex;
  gap: 8px;
  justify-content: flex-end;
  margin-top: 8px;
}

.btn-edit {
  background: #ffc107;
  color: #000;
  border: none;
  padding: 8px 12px;
  border-radius: 8px;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-weight: 600;
  transition: 0.2s;
}

.btn-edit:hover {
  background: #e0a800;
}

body.dark .btn-edit {
  background: #facc15;
  color: #111;
}

body.dark .btn-edit:hover {
  background: #eab308;
}

.danger {
  background: #dc3545;
  color: #fff;
  border: none;
  padding: 8px 12px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: 0.2s;
}

.danger:hover {
  background: #b52d29;
}

body.dark .danger {
  background: #ef4444;
}

body.dark .danger:hover {
  background: #dc2626;
}
</style>


<?php include 'includes/footer.php'; ?>
