<?php
require 'config.php';
$user = require_login($db);
$pageTitle = "Powiadomienia";
include 'includes/header.php';

// pobierz powiadomienia użytkownika
$stmt = $db->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user['id']]);
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// oznacz jako przeczytane
$db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?")->execute([$user['id']]);
?>
<section class="notifications">
  <h1>Powiadomienia</h1>
  <?php if ($notes): ?>
    <?php foreach ($notes as $n): ?>
      <div class="card <?= $n['is_read'] ? 'read' : 'unread' ?>">
        <p><?= htmlspecialchars($n['content']) ?></p>
        <?php if ($n['link']): ?>
          <a href="<?= htmlspecialchars($n['link']) ?>">Zobacz</a>
        <?php endif; ?>
        <small><?= date('d.m.Y H:i', strtotime($n['created_at'])) ?></small>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p>Brak powiadomień</p>
  <?php endif; ?>
</section>
<?php include 'includes/footer.php'; ?>
