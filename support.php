<?php
require 'config.php';
$user = require_login($db);
$pageTitle = "Pomoc techniczna";
include 'includes/header.php';

// Zgłoszenie nowego ticketa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $subject = trim($_POST['subject']);
  $message = trim($_POST['message']);
  if ($subject && $message) {
    $stmt = $db->prepare("INSERT INTO support_tickets (user_id, subject, message) VALUES (?, ?, ?)");
    $stmt->execute([$user['id'], $subject, $message]);
    $notice = "✅ Zgłoszenie zostało wysłane do działu wsparcia.";
  } else {
    $error = "❌ Wypełnij wszystkie pola.";
  }
}

// Pobierz zgłoszenia użytkownika
$stmt = $db->prepare("SELECT * FROM support_tickets WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user['id']]);
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="support">
  <h1>💬 Centrum wsparcia</h1>

  <?php if (!empty($notice)): ?><div class="notice"><?= $notice ?></div><?php endif; ?>
  <?php if (!empty($error)): ?><div class="error"><?= $error ?></div><?php endif; ?>

  <form method="post" class="card support-form">
    <h2>Nowe zgłoszenie</h2>
    <label>Temat</label>
    <input type="text" name="subject" required>
    <label>Opis problemu</label>
    <textarea name="message" required rows="4"></textarea>
    <button type="submit" class="btn">📨 Wyślij</button>
  </form>

  <div class="ticket-list">
    <h2>Twoje zgłoszenia</h2>
    <?php if ($tickets): ?>
      <?php foreach ($tickets as $t): ?>
        <div class="ticket card">
          <h3><?= htmlspecialchars($t['subject']) ?></h3>
          <p><?= nl2br(htmlspecialchars($t['message'])) ?></p>
          <p><strong>Status:</strong>
            <?php if ($t['status'] === 'open'): ?>
              <span class="status open">Oczekuje</span>
            <?php elseif ($t['status'] === 'answered'): ?>
              <span class="status answered">Odpowiedziano</span>
            <?php else: ?>
              <span class="status closed">Zamknięte</span>
            <?php endif; ?>
          </p>
          <?php if ($t['admin_reply']): ?>
            <div class="reply">
              <strong>Odpowiedź administratora:</strong><br>
              <?= nl2br(htmlspecialchars($t['admin_reply'])) ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Nie masz jeszcze żadnych zgłoszeń.</p>
    <?php endif; ?>
  </div>
</section>

<style>
.support { max-width: 900px; margin: 40px auto; padding: 0 20px; font-family: 'Inter', sans-serif; }
.support h1 { font-size: 1.8rem; margin-bottom: 20px; color: var(--primary-dark, #073b66); }
.card { background:#fff; border-radius:12px; padding:20px; box-shadow:0 4px 12px rgba(0,0,0,0.1); margin-bottom:20px; }
body.dark .card { background:#161b22; color:#e5e7eb; border:1px solid #2c2f33; }
.notice { background:#e9f7ef; border:1px solid #c7eed2; color:#155724; padding:10px; border-radius:8px; margin-bottom:12px; }
.error { background:#fdecea; border:1px solid #f5c2c7; color:#b02a37; padding:10px; border-radius:8px; margin-bottom:12px; }
.support-form input, .support-form textarea {
  width:100%; padding:10px; border-radius:8px; border:1px solid #ccc; margin-bottom:10px;
}
body.dark .support-form input, body.dark .support-form textarea {
  background:#0f172a; border-color:#334155; color:#e5e7eb;
}
.btn { background:#007bff; color:#fff; border:none; padding:10px 16px; border-radius:8px; cursor:pointer; }
.btn:hover { background:#0056b3; }
.ticket h3 { margin:0 0 5px; color:#0046ad; }
.status { font-weight:bold; padding:2px 8px; border-radius:6px; }
.status.open { background:#ffeeba; color:#856404; }
.status.answered { background:#bee3f8; color:#084298; }
.status.closed { background:#d4edda; color:#155724; }
.reply { background:rgba(0,123,255,0.1); border-left:4px solid #007bff; padding:8px 12px; border-radius:6px; margin-top:10px; }
</style>

<?php include 'includes/footer.php'; ?>
