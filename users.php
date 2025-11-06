<?php
require 'config.php';
$user = require_login($db);
$pageTitle = "Użytkownicy społeczności";
include 'includes/header.php';

// Pobierz wszystkich użytkowników
$stmt = $db->query("
  SELECT 
    u.id, u.first_name, u.last_name, u.avatar, u.bio, u.created_at,
    COUNT(DISTINCT c.id) AS cars_count,
    COUNT(DISTINCT p.id) AS posts_count
  FROM users u
  LEFT JOIN cars c ON c.user_id = u.id
  LEFT JOIN posts p ON p.user_id = u.id
  GROUP BY u.id
  ORDER BY u.created_at DESC
");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="users-page">
  <h1>Użytkownicy społeczności</h1>
  <p>Przeglądaj profile i poznaj innych członków AutoPart Community.</p>

  <div class="users-grid">
    <?php foreach ($users as $u): ?>
      <div class="user-card card">
        <img src="<?= htmlspecialchars($u['avatar'] ?: 'default-avatar.png') ?>" class="avatar-user" alt="Avatar">
        <h3><?= htmlspecialchars($u['first_name'].' '.$u['last_name']) ?></h3>
        <p class="bio"><?= $u['bio'] ? htmlspecialchars($u['bio']) : '<em>Brak opisu</em>' ?></p>
        <div class="user-stats">
          <span><i class="fa-solid fa-car"></i> <?= $u['cars_count'] ?> aut</span>
          <span><i class="fa-solid fa-newspaper"></i> <?= $u['posts_count'] ?> postów</span>
        </div>
        <a href="profile.php?id=<?= $u['id'] ?>" class="btn-small">Zobacz profil</a>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
