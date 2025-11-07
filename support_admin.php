<?php
require 'config.php';
$user = require_login($db);
if ($user['role'] !== 'admin') {
  header("Location: dashboard.php");
  exit;
}

$pageTitle = "Zgłoszenia użytkowników";
include 'includes/header.php';

// Odpowiedź admina
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = (int)$_POST['id'];
  $reply = trim($_POST['reply']);
  $stmt = $db->prepare("UPDATE support_tickets SET admin_reply = ?, status = 'answered' WHERE id = ?");
  $stmt->execute([$reply, $id]);
  $notice = "✅ Odpowiedź została wysłana.";
}

// Pobierz wszystkie zgłoszenia
$tickets = $db->query("
  SELECT s.*, u.first_name, u.last_name, u.email
  FROM support_tickets s
  JOIN users u ON s.user_id = u.id
  ORDER BY s.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="support-admin">
  <h1>🎧 Zgłoszenia użytkowników</h1>
  <?php if (!empty($notice)): ?><div class="notice"><?= $notice ?></div><?php endif; ?>

  <?php foreach ($tickets as $t): ?>
    <div class="ticket card">
      <h3><?= htmlspecialchars($t['subject']) ?> 
        <small>(<?= htmlspecialchars($t['first_name'].' '.$t['last_name']) ?> – <?= htmlspecialchars($t['email']) ?>)</small>
      </h3>
      <p><?= nl2br(htmlspecialchars($t['message'])) ?></p>
      <p><strong>Status:</strong> <?= $t['status'] ?></p>

      <?php if ($t['admin_reply']): ?>
        <div class="reply"><strong>Odpowiedź:</strong><br><?= nl2br(htmlspecialchars($t['admin_reply'])) ?></div>
      <?php endif; ?>

      <form method="post" class="reply-form">
        <input type="hidden" name="id" value="<?= $t['id'] ?>">
        <textarea name="reply" placeholder="Odpowiedź..." required rows="3"></textarea>
        <button type="submit" class="btn">✉️ Wyślij odpowiedź</button>
      </form>
    </div>
  <?php endforeach; ?>
</section>

<style>
.support-admin { max-width: 900px; margin: 40px auto; padding: 0 20px; font-family: 'Inter', sans-serif; }
.ticket.card { margin-bottom:20px; background:#fff; border-radius:12px; padding:16px; box-shadow:0 4px 12px rgba(0,0,0,0.1); }
body.dark .ticket.card { background:#161b22; color:#e5e7eb; border:1px solid #2c2f33; }
.reply-form textarea { width:100%; border-radius:8px; border:1px solid #ccc; padding:8px; margin-top:8px; }
body.dark .reply-form textarea { background:#0f172a; border-color:#334155; color:#e5e7eb; }
.btn { background:#007bff; color:#fff; border:none; padding:8px 14px; border-radius:8px; cursor:pointer; margin-top:8px; }
.notice { background:#e9f7ef; border:1px solid #c7eed2; color:#155724; padding:10px; border-radius:8px; margin-bottom:12px; }
</style>

<?php include 'includes/footer.php'; ?>
