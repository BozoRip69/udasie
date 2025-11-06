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
  'cars' => $db->query("SELECT COUNT(*) FROM cars")->fetchColumn(),
  'batteries' => $db->query("SELECT COUNT(*) FROM batteries")->fetchColumn(),
  'posts' => $db->query("SELECT COUNT(*) FROM posts")->fetchColumn(),
  'comments' => $db->query("SELECT COUNT(*) FROM comments")->fetchColumn(),
  'messages' => $db->query("SELECT COUNT(*) FROM messages")->fetchColumn()
];

// lista użytkowników
$users = $db->query("SELECT id, first_name, last_name, email, role, created_at FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// lista postów
$posts = $db->query("
  SELECT p.id, p.content, p.created_at, u.first_name, u.last_name 
  FROM posts p JOIN users u ON u.id = p.user_id
  ORDER BY p.created_at DESC LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="admin-panel">
  <h1>Panel administratora</h1>
  <p>Zarządzaj użytkownikami, postami i statystykami systemu.</p>

  <div class="admin-stats">
    <div class="stat-card"><i class="fa-solid fa-users"></i><strong><?= $stats['users'] ?></strong><span>Użytkownicy</span></div>
    <div class="stat-card"><i class="fa-solid fa-car"></i><strong><?= $stats['cars'] ?></strong><span>Samochody</span></div>
    <div class="stat-card"><i class="fa-solid fa-battery-half"></i><strong><?= $stats['batteries'] ?></strong><span>Akumulatory</span></div>
    <div class="stat-card"><i class="fa-solid fa-newspaper"></i><strong><?= $stats['posts'] ?></strong><span>Posty</span></div>
    <div class="stat-card"><i class="fa-solid fa-comment"></i><strong><?= $stats['comments'] ?></strong><span>Komentarze</span></div>
    <div class="stat-card"><i class="fa-solid fa-envelope"></i><strong><?= $stats['messages'] ?></strong><span>Wiadomości</span></div>
  </div>

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
            <td><?= $u['role'] ?></td>
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

<?php include 'includes/footer.php'; ?>
