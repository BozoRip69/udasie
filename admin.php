<?php
require 'config.php';
$user = require_login($db);

// tylko dla admina
if ($user['role'] !== 'admin') {
  header("Location: dashboard.php");
  exit;
}

$pageTitle = "Panel administratora";
include 'includes/header.php';

// Statystyki
$stats = [
  'users' => $db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
  'vehicles' => $db->query("SELECT COUNT(*) FROM vehicles")->fetchColumn(),
  'user_batteries' => $db->query("SELECT COUNT(*) FROM user_batteries")->fetchColumn(),
  'posts' => $db->query("SELECT COUNT(*) FROM posts")->fetchColumn(),
  'comments' => $db->query("SELECT COUNT(*) FROM comments")->fetchColumn(),
  'messages' => $db->query("SELECT COUNT(*) FROM messages")->fetchColumn()
];

// licznik otwartych zgłoszeń supportu
$supportCount = $db->query("SELECT COUNT(*) FROM support_tickets WHERE status = 'open'")->fetchColumn();

// lista użytkowników
$users = $db->query("
  SELECT id, first_name, last_name, email, role, created_at 
  FROM users ORDER BY created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

// lista postów
$posts = $db->query("
  SELECT p.id, p.content, p.created_at, u.first_name, u.last_name 
  FROM posts p 
  JOIN users u ON u.id = p.user_id
  ORDER BY p.created_at DESC LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

// lista baterii użytkowników
$user_batteries = $db->query("
  SELECT ub.id, b.serial_number, b.conductor_number, b.battery_model, b.installation_date, 
         v.vin, v.brand, v.model, u.first_name, u.last_name 
  FROM user_batteries ub 
  JOIN users u ON u.id = ub.user_id
  JOIN batteries b ON b.id = ub.battery_id
  JOIN vehicles v ON v.id = ub.vehicle_id
  ORDER BY b.installation_date DESC
")->fetchAll(PDO::FETCH_ASSOC);

// lista samochodów
$vehicles = $db->query("
  SELECT v.id, v.brand, v.model, v.vin, v.mileage, v.purchase_date, v.inspection_date, 
         u.first_name, u.last_name, u.email
  FROM vehicles v 
  JOIN users u ON u.id = v.user_id
  ORDER BY v.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="admin-panel">
  <h1>Panel administratora</h1>
  <p>Zarządzaj użytkownikami, samochodami, akumulatorami i postami.</p>

  <!-- 🔹 LINK DO PANELU WSPARCIA -->
  <div class="admin-links">
    <a href="support_admin.php" class="btn-support">
      <i class="fa-solid fa-headset"></i> Centrum wsparcia 
      <?php if ($supportCount > 0): ?>
        <span class="badge"><?= $supportCount ?></span>
      <?php endif; ?>
    </a>
  </div>

  <div class="admin-stats">
    <div class="stat-card"><i class="fa-solid fa-users"></i><strong><?= $stats['users'] ?></strong><span>Użytkownicy</span></div>
    <div class="stat-card"><i class="fa-solid fa-car"></i><strong><?= $stats['vehicles'] ?></strong><span>Samochody</span></div>
    <div class="stat-card"><i class="fa-solid fa-battery-half"></i><strong><?= $stats['user_batteries'] ?></strong><span>Akumulatory</span></div>
    <div class="stat-card"><i class="fa-solid fa-newspaper"></i><strong><?= $stats['posts'] ?></strong><span>Posty</span></div>
    <div class="stat-card"><i class="fa-solid fa-comment"></i><strong><?= $stats['comments'] ?></strong><span>Komentarze</span></div>
    <div class="stat-card"><i class="fa-solid fa-envelope"></i><strong><?= $stats['messages'] ?></strong><span>Wiadomości</span></div>
  </div>

  <!-- =========================== -->
  <!-- Sekcja SAMOCHODY UŻYTKOWNIKÓW -->
  <!-- =========================== -->
  <div class="admin-section">
    <h2>Samochody użytkowników</h2>
    <table class="admin-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Użytkownik</th>
          <th>Marka i model</th>
          <th>VIN</th>
          <th>Przebieg</th>
          <th>Zakup</th>
          <th>Przegląd</th>
          <th>Akcje</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($vehicles as $v): ?>
          <tr>
            <td><?= $v['id'] ?></td>
            <td><?= htmlspecialchars($v['first_name'].' '.$v['last_name']) ?><br><small><?= htmlspecialchars($v['email']) ?></small></td>
            <td><?= htmlspecialchars($v['brand'].' '.$v['model']) ?></td>
            <td><?= htmlspecialchars($v['vin']) ?></td>
            <td><?= number_format((int)$v['mileage'], 0, ',', ' ') ?> km</td>
            <td><?= htmlspecialchars($v['purchase_date'] ?: '-') ?></td>
            <td><?= htmlspecialchars($v['inspection_date'] ?: '-') ?></td>
            <td>
              <form action="admin_action.php" method="post" style="display:inline;">
                <input type="hidden" name="vehicle_id" value="<?= $v['id'] ?>">
                <button name="action" value="force_update_mileage" class="btn-small btn-warning" title="Wymuś aktualizację przebiegu">
                  <i class="fa-solid fa-gauge-high"></i> Wymuś
                </button>
              </form>
              <form action="admin_action.php" method="post" style="display:inline;">
                <input type="hidden" name="id" value="<?= $v['id'] ?>">
                <button name="action" value="delete_vehicle" class="danger btn-small" onclick="return confirm('Usunąć ten pojazd?')">
                  <i class="fa-solid fa-trash"></i>
                </button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- =========================== -->
  <!-- Sekcja AKUMULATORY -->
  <!-- =========================== -->
  <div class="admin-section">
    <h2>Akumulatory użytkowników</h2>
    <table class="admin-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Numer seryjny</th>
          <th>Numer przewodnika</th>
          <th>Model</th>
          <th>Data instalacji</th>
          <th>VIN pojazdu</th>
          <th>Pojazd</th>
          <th>Właściciel</th>
          <th>Akcje</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($user_batteries as $ub): ?>
          <tr>
            <td><?= $ub['id'] ?></td>
            <td><?= htmlspecialchars($ub['serial_number']) ?></td>
            <td><?= htmlspecialchars($ub['conductor_number']) ?></td>
            <td><?= htmlspecialchars($ub['battery_model']) ?></td>
            <td><?= htmlspecialchars($ub['installation_date'] ?: '-') ?></td>
            <td><?= htmlspecialchars($ub['vin']) ?></td>
            <td><?= htmlspecialchars($ub['brand'].' '.$ub['model']) ?></td>
            <td><?= htmlspecialchars($ub['first_name'].' '.$ub['last_name']) ?></td>
            <td>
              <form action="admin_action.php" method="post" style="display:inline;">
                <input type="hidden" name="id" value="<?= $ub['id'] ?>">
                <button name="action" value="delete_user_battery" class="danger btn-small" onclick="return confirm('Usunąć tę baterię?')">
                  <i class="fa-solid fa-trash"></i>
                </button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- =========================== -->
  <!-- Sekcja UŻYTKOWNICY -->
  <!-- =========================== -->
  <div class="admin-section">
    <h2>Użytkownicy</h2>
    <table class="admin-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Imię i nazwisko</th>
          <th>Email</th>
          <th>Rola</th>
          <th>Data rejestracji</th>
          <th>Akcje</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
          <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['first_name'].' '.$u['last_name']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= htmlspecialchars($u['role']) ?></td>
            <td><?= date('d.m.Y H:i', strtotime($u['created_at'])) ?></td>
            <td>
              <?php if ($u['id'] !== $user['id']): ?>
                <form action="admin_action.php" method="post" style="display:inline;">
                  <input type="hidden" name="id" value="<?= $u['id'] ?>">
                  <button name="action" value="delete_user" class="danger btn-small" onclick="return confirm('Usunąć użytkownika?')">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                </form>
              <?php else: ?>
                <em>Ty</em>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- =========================== -->
  <!-- Sekcja POSTY -->
  <!-- =========================== -->
  <div class="admin-section">
    <h2>Ostatnie posty</h2>
    <table class="admin-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Autor</th>
          <th>Treść</th>
          <th>Data</th>
          <th>Akcja</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($posts as $p): ?>
          <tr>
            <td><?= $p['id'] ?></td>
            <td><?= htmlspecialchars($p['first_name'].' '.$p['last_name']) ?></td>
            <td><?= htmlspecialchars(substr($p['content'],0,60)) ?>...</td>
            <td><?= date('d.m.Y H:i', strtotime($p['created_at'])) ?></td>
            <td>
              <form action="admin_action.php" method="post" style="display:inline;">
                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                <button name="action" value="delete_post" class="danger btn-small" onclick="return confirm('Usunąć post?')">
                  <i class="fa-solid fa-trash"></i>
                </button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<style>
.admin-links {
  margin: 20px 0;
  text-align: center;
}
.btn-support {
  background: #007bff;
  color: white;
  padding: 10px 18px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: all 0.2s ease;
  position: relative;
}
.btn-support:hover {
  background: #0056b3;
  transform: translateY(-1px);
}
.badge {
  background: #dc3545;
  color: white;
  font-size: 0.8rem;
  border-radius: 50%;
  padding: 2px 7px;
  margin-left: 4px;
}
body.dark .btn-support {
  background: #2563eb;
  color: #e5e7eb;
}
body.dark .btn-support:hover {
  background: #1e40af;
}
</style>

<?php include 'includes/footer.php'; ?>
