<?php
require 'config.php';
$user = require_login($db);
$pageTitle = "Profil użytkownika";
include 'includes/header.php';

$profile_id = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare("
  SELECT u.*, 
    COUNT(DISTINCT c.id) AS cars_count,
    COUNT(DISTINCT p.id) AS posts_count
  FROM users u
  LEFT JOIN cars c ON c.user_id = u.id
  LEFT JOIN posts p ON p.user_id = u.id
  WHERE u.id = ?
  GROUP BY u.id
");
$stmt->execute([$profile_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profile) {
  echo "<p>Nie znaleziono użytkownika.</p>";
  include 'includes/footer.php';
  exit;
}
?>

<section class="profile-page">
  <div class="profile-header card">
    <img src="<?= htmlspecialchars($profile['avatar'] ?: 'default-avatar.png') ?>" class="avatar-large" alt="Avatar">
    <div>
      <h2><?= htmlspecialchars($profile['first_name'].' '.$profile['last_name']) ?></h2>
      <p><strong>Email:</strong> <?= htmlspecialchars($profile['email']) ?></p>
      <p><strong>Telefon:</strong> <?= htmlspecialchars($profile['country_code'].' '.$profile['phone']) ?></p>
      <p><strong>Adres:</strong> <?= htmlspecialchars($profile['address']) ?></p>
      <p><strong>Opis:</strong> <?= $profile['bio'] ? htmlspecialchars($profile['bio']) : 'Brak opisu.' ?></p>
      <div class="user-stats">
        <span><i class="fa-solid fa-car"></i> <?= $profile['cars_count'] ?> samochodów</span>
        <span><i class="fa-solid fa-newspaper"></i> <?= $profile['posts_count'] ?> postów</span>
      </div>
      <?php if ($profile['id'] !== $user['id']): ?>
        <form action="messages.php" method="get">
          <input type="hidden" name="user_id" value="<?= $profile['id'] ?>">
          <button type="submit" class="btn-small">Wyślij wiadomość</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
