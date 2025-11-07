<?php
require 'config.php';
$user = require_login($db);
$pageTitle = "Twój garaż";
include 'includes/header.php';

// Pobierz pojazdy użytkownika
$stmt = $db->prepare("SELECT * FROM vehicles WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user['id']]);
$vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Oblicz łączny przebieg
$totalMileage = 0;
foreach ($vehicles as $v) {
    $totalMileage += (int)($v['mileage'] ?? 0);
}

// Powiadomienia
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
        <?php
        // Ostatni wpis przebiegu
        $stmt2 = $db->prepare("
          SELECT mileage, recorded_at 
          FROM vehicle_mileage 
          WHERE vehicle_id = ? 
          ORDER BY recorded_at DESC LIMIT 1
        ");
        $stmt2->execute([$v['id']]);
        $last = $stmt2->fetch(PDO::FETCH_ASSOC);

        // Czy trzeba zaktualizować
        $needsUpdate = false;
        if (!empty($v['needs_update']) && $v['needs_update'] == 1) {
            $needsUpdate = true;
        } elseif (!$last || strtotime($last['recorded_at']) < strtotime('-30 days')) {
            $needsUpdate = true;
        }
        ?>
        <div class="car-card card">
          <div class="car-top">
            <div class="car-thumb-wrap">
              <img class="car-thumb" src="<?= htmlspecialchars($v['image'] ?: 'uploads/avatars/default-car.png') ?>" alt="<?= htmlspecialchars($v['brand'].' '.$v['model']) ?>">
            </div>
            <div class="car-meta">
              <h3><?= htmlspecialchars($v['brand'] . ' ' . $v['model']) ?></h3>
              <p><strong>VIN:</strong> <?= htmlspecialchars($v['vin'] ?: '-') ?></p>
              <p><strong>Rok:</strong> <?= htmlspecialchars($v['year'] ?: '-') ?></p>
              <p><strong>Rejestracja:</strong> <?= htmlspecialchars($v['registration_number'] ?: '-') ?></p>

              <?php if ($needsUpdate): ?>
                <div class="alert-update">
                  <i class="fa-solid fa-triangle-exclamation"></i> 
                  <strong>Wymagana aktualizacja przebiegu!</strong><br>
                  <a href="update_mileage.php?vehicle_id=<?= $v['id'] ?>" class="btn-small">🔧 Zaktualizuj</a>
                </div>
              <?php endif; ?>
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

          <canvas id="chart-<?= $v['id'] ?>" height="180"></canvas>

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
.garage { max-width: 1100px; margin: 40px auto; padding: 0 20px; font-family: 'Inter', sans-serif; }
.garage-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
.garage-header h1 { color: var(--primary-dark, #073b66); font-size:1.9rem; font-weight:700; }
body.dark .garage-header h1 { color:#60a5fa; }
.garage-actions .btn-vin { background:#007bff; color:white; padding:10px 18px; border-radius:8px; text-decoration:none; font-weight:600; }
.notice { background:#e9f7ef; border:1px solid #c7eed2; color:#155724; padding:10px; border-radius:8px; margin-bottom:12px; }
body.dark .notice { background:rgba(46,204,113,0.1); border-color:#16a34a; color:#4ade80; }
.garage-summary { text-align:center; margin-bottom:20px; }
.garage-summary .highlight { color:#28a745; font-weight:bold; }
body.dark .garage-summary .highlight { color:#4ade80; }
.garage-list { display:grid; grid-template-columns:repeat(auto-fit,minmax(340px,1fr)); gap:20px; }
.car-card { background:#fff; border-radius:12px; padding:16px; box-shadow:0 6px 18px rgba(17,24,39,0.06); border:1px solid #f1f5f9; transition:0.25s; }
.car-card:hover { transform:translateY(-3px); box-shadow:0 8px 20px rgba(0,123,255,0.15); }
body.dark .car-card { background:#161b22; border-color:#2c2f33; box-shadow:none; }
.car-top { display:flex; gap:16px; align-items:flex-start; }
.car-thumb { width:140px; height:140px; border-radius:10px; object-fit:cover; border:1px solid #e2e8f0; box-shadow:0 4px 12px rgba(0,0,0,0.08); }
body.dark .car-thumb { border-color:#2c2f33; box-shadow:0 4px 10px rgba(255,255,255,0.05); }
.car-meta h3 { margin:0 0 6px 0; color:var(--primary,#0046ad); }
body.dark .car-meta h3 { color:#3b82f6; }
.car-meta p { color:#555; margin:0; font-size:0.95rem; }
body.dark .car-meta p { color:#e5e7eb; }
.car-info { display:grid; grid-template-columns:1fr 1fr; list-style:none; padding:0; margin:0; font-size:0.95rem; color:#444; gap:6px 20px; }
body.dark .car-info { color:#e5e7eb; }
.alert-update { background:rgba(255,193,7,0.15); color:#d97706; padding:8px 12px; border-radius:8px; margin-top:8px; text-align:center; font-weight:600; }
body.dark .alert-update { background:rgba(234,179,8,0.2); color:#facc15; }
.car-actions { display:flex; justify-content:flex-end; gap:8px; margin-top:8px; }
.btn-edit { background:#ffc107; color:#000; padding:8px 12px; border-radius:8px; text-decoration:none; font-weight:600; }
body.dark .btn-edit { background:#facc15; color:#111; }
.danger { background:#dc3545; color:#fff; border:none; padding:8px 12px; border-radius:8px; font-weight:600; cursor:pointer; }
body.dark .danger { background:#ef4444; }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
<?php foreach ($vehicles as $v):
  $stmt3 = $db->prepare("SELECT mileage, recorded_at FROM vehicle_mileage WHERE vehicle_id = ? ORDER BY recorded_at ASC");
  $stmt3->execute([$v['id']]);
  $data = $stmt3->fetchAll(PDO::FETCH_ASSOC);
?>
(function() {
  const ctx = document.getElementById('chart-<?= $v['id'] ?>');
  const chartData = {
    labels: <?= json_encode(array_column($data, 'recorded_at')) ?>,
    datasets: [{
      label: 'Przebieg (km)',
      data: <?= json_encode(array_column($data, 'mileage')) ?>,
      borderWidth: 2,
      tension: 0.3,
      fill: true
    }]
  };

  function createChart(dark) {
    const lineColor = dark ? '#4ade80' : '#007bff';
    const bgColor = dark ? 'rgba(74,222,128,0.15)' : 'rgba(0,123,255,0.15)';
    const gridColor = dark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.05)';
    const textColor = dark ? '#e5e7eb' : '#111';
    return new Chart(ctx, {
      type: 'line',
      data: chartData,
      options: {
        plugins: { legend: { display: false } },
        scales: {
          x: { ticks: { color: textColor }, grid: { color: gridColor } },
          y: { ticks: { color: textColor }, grid: { color: gridColor } }
        },
        elements: {
          line: { borderColor: lineColor, backgroundColor: bgColor },
          point: { backgroundColor: lineColor }
        }
      }
    });
  }

  let chart = createChart(document.body.classList.contains('dark'));
  const observer = new MutationObserver(() => {
    chart.destroy();
    chart = createChart(document.body.classList.contains('dark'));
  });
  observer.observe(document.body, { attributes:true, attributeFilter:['class'] });
})();
<?php endforeach; ?>
</script>

<?php include 'includes/footer.php'; ?>
