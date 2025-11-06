<?php 
require 'config.php';
$user = require_login($db);
$pageTitle = "Wiadomości";
include 'includes/header.php';

// 🟢 Lista użytkowników z licznikiem nieprzeczytanych wiadomości
$stmt = $db->prepare("
  SELECT u.id, u.first_name, u.last_name, u.avatar,
    (SELECT COUNT(*) FROM messages m WHERE m.sender_id = u.id AND m.receiver_id = ? AND m.is_read = 0) AS unread_count
  FROM users u
  WHERE u.id != ?
  ORDER BY unread_count DESC, u.first_name
");
$stmt->execute([$user['id'], $user['id']]);
$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 🟢 Wybrany rozmówca
$chat_id = isset($_GET['user']) ? (int)$_GET['user'] : null;
$chat_user = null;
if ($chat_id) {
    $stmt = $db->prepare("SELECT id, first_name, last_name, avatar FROM users WHERE id = ?");
    $stmt->execute([$chat_id]);
    $chat_user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 🔵 Oznacz wiadomości jako przeczytane po wejściu w czat
    $db->prepare("UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ?")
       ->execute([$chat_id, $user['id']]);
}

// 🟢 Wysyłanie wiadomości
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message']) && $chat_id) {
    $content = trim($_POST['message']);
    if ($content !== '') {
        $stmt = $db->prepare("
            INSERT INTO messages (sender_id, receiver_id, content, created_at, is_read)
            VALUES (?, ?, ?, NOW(), 0)
        ");
        $stmt->execute([$user['id'], $chat_id, $content]);
    }
    header("Location: messages.php?user=$chat_id");
    exit;
}
?>

<main class="messages-wrapper">
  <div class="messages-container">
    <aside class="contacts">
      <h2>Rozmowy</h2>
      <?php foreach ($contacts as $c): ?>
        <a href="messages.php?user=<?= $c['id'] ?>" class="contact <?= $chat_id == $c['id'] ? 'active' : '' ?>">
          <img src="<?= htmlspecialchars($c['avatar'] ?: 'default-avatar.png') ?>" class="avatar-small" alt="">
          <span><?= htmlspecialchars($c['first_name'].' '.$c['last_name']) ?></span>
          <?php if ($c['unread_count'] > 0): ?>
            <span class="unread-badge"><?= $c['unread_count'] ?></span>
          <?php endif; ?>
        </a>
      <?php endforeach; ?>
    </aside>

    <div class="chat-area">
      <?php if ($chat_user): ?>
        <div class="chat-header">
          <img src="<?= htmlspecialchars($chat_user['avatar'] ?: 'default-avatar.png') ?>" class="avatar-small" alt="">
          <h3><?= htmlspecialchars($chat_user['first_name'].' '.$chat_user['last_name']) ?></h3>
        </div>

        <div class="chat-messages" id="chat-box">
          <?php
          $stmt = $db->prepare("
            SELECT * FROM messages 
            WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
            ORDER BY created_at ASC
          ");
          $stmt->execute([$user['id'], $chat_id, $chat_id, $user['id']]);
          $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
          ?>

          <?php foreach ($messages as $msg): ?>
            <div class="msg <?= $msg['sender_id'] == $user['id'] ? 'sent' : 'recv' ?>">
              <div class="msg-content"><?= nl2br(htmlspecialchars($msg['content'])) ?></div>
              <small class="msg-time"><?= date('d.m.Y H:i', strtotime($msg['created_at'])) ?></small>
            </div>
          <?php endforeach; ?>
        </div>

        <form method="post" class="send-form">
          <input type="text" name="message" placeholder="Napisz wiadomość..." required autocomplete="off">
          <button type="submit"><i class="fa-solid fa-paper-plane"></i></button>
        </form>

        <script>
          // automatyczne przewinięcie do dołu
          const chatBox = document.getElementById('chat-box');
          if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;

          // 🔵 po wejściu do konwersacji — zaktualizuj licznik wiadomości
          <?php if ($chat_id): ?>
          fetch('update_unread.php')
            .then(r => r.json())
            .then(data => {
              const badge = document.getElementById('msg-counter');
              if (!badge) return;
              if (data.total > 0) {
                badge.textContent = data.total;
              } else {
                badge.style.display = 'none';
              }
            })
            .catch(e => console.error('Błąd aktualizacji licznika:', e));
          <?php endif; ?>
        </script>
        <script>
// 🔄 Automatyczna aktualizacja liczników rozmów co 3 sekundy
setInterval(() => {
  fetch('check_messages.php')
    .then(r => r.json())
    .then(data => {
      // Zeruj wszystkie liczniki
      document.querySelectorAll('.unread-badge').forEach(b => b.remove());

      // Dodaj badge tam, gdzie są nowe wiadomości
      data.forEach(item => {
        const contact = document.querySelector(`.contact[href="messages.php?user=${item.user_id}"]`);
        if (contact && item.unread_count > 0) {
          const badge = document.createElement('span');
          badge.className = 'unread-badge';
          badge.textContent = item.unread_count;
          contact.appendChild(badge);
        }
      });
    })
    .catch(err => console.error('Błąd aktualizacji rozmów:', err));
}, 3000); // co 3 sekundy
</script>

      <?php else: ?>
        <div class="chat-empty">
          <p>Wybierz rozmówcę, aby rozpocząć czat 💬</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</main>


<?php include 'includes/footer.php'; ?>
