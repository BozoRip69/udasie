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

        <div class="chat-messages" id="chat-box"></div>

        <!-- 📎 Formularz z polem do zdjęć -->
        <form id="sendForm" class="send-form" enctype="multipart/form-data">
          <input type="text" name="message" placeholder="Napisz wiadomość..." autocomplete="off">
          <label class="file-btn">
            <i class="fa-solid fa-image"></i>
            <input type="file" name="image" accept="image/*" style="display:none;">
          </label>
          <button type="submit"><i class="fa-solid fa-paper-plane"></i></button>
        </form>

        <script>
        const chatBox = document.getElementById('chat-box');
        const chatUser = <?= json_encode($chat_id) ?>;
        let lastMessageCount = 0;

        // 🔄 Pobieranie wiadomości AJAX-em
        function refreshMessages() {
          if (!chatUser) return;

          fetch('messages_fetch.php?user=' + chatUser)
            .then(r => r.json())
            .then(messages => {
              if (!messages.length) {
                chatBox.innerHTML = '<p class="empty-chat">Brak wiadomości.</p>';
                return;
              }

              // 🧠 jeśli liczba wiadomości się nie zmieniła — nie rób nic
              if (messages.length === lastMessageCount) return;

              // 🔁 renderuj tylko nowe wiadomości
              const newMessages = messages.slice(lastMessageCount);
              lastMessageCount = messages.length;

              // jeśli czat pusty — załaduj wszystko
              if (chatBox.children.length === 0) {
                messages.forEach(msg => addMessage(msg, false));
              } else {
                newMessages.forEach(msg => addMessage(msg, true));
              }

              chatBox.scrollTo({ top: chatBox.scrollHeight, behavior: 'smooth' });
            })
            .catch(err => console.error('Błąd pobierania wiadomości:', err));
        }

        // 🧩 Tworzenie wiadomości z animacją
        function addMessage(msg, animate = true) {
          const div = document.createElement('div');
          div.classList.add('msg');
          div.classList.add(msg.sender_id == <?= $user['id'] ?> ? 'sent' : 'recv');

          // treść wiadomości
          let html = '';
          if (msg.content) {
            html += `<div class="msg-content">${msg.content.replace(/\n/g, '<br>')}</div>`;
          }
if (msg.image_path) {
  html += `
    <div class="msg-image">
      <img src="${msg.image_path}" alt="Wysłane zdjęcie" data-full="${msg.image_path}">
    </div>`;
}

          html += `<small class="msg-time">${new Date(msg.created_at).toLocaleString('pl-PL')}</small>`;
          div.innerHTML = html;

          // ✨ efekt animacji
          if (animate) {
            div.classList.add('fade-in');
            setTimeout(() => div.classList.remove('fade-in'), 600);
          }

          chatBox.appendChild(div);
        }

        // 🔁 Odświeżanie czatu co 2 sekundy
        setInterval(refreshMessages, 2000);
        refreshMessages();

        // ✉️ Wysyłanie wiadomości AJAX-em (tekst + zdjęcie)
        const form = document.getElementById('sendForm');
        form.addEventListener('submit', function(e) {
          e.preventDefault();
          const formData = new FormData(form);
          formData.append('receiver_id', chatUser);

          fetch('messages_send.php', {
            method: 'POST',
            body: formData
          })
          .then(r => r.text())
          .then(() => {
            form.reset();
            refreshMessages(); // odśwież natychmiast
          })
          .catch(err => console.error('Błąd wysyłania wiadomości:', err));
        });
        </script>
        <script>
// 🖼️ Lightbox – kliknięcie w miniaturkę zdjęcia w czacie
document.addEventListener('click', function(e) {
  if (e.target.matches('.msg-image img')) {
    const src = e.target.getAttribute('src');
    const lightbox = document.getElementById('lightbox');
    const lightboxImg = lightbox.querySelector('img');
    lightboxImg.src = src;
    lightbox.classList.add('active');
  }
});

// ❌ Zamknięcie lightboxa kliknięciem lub klawiszem ESC
document.getElementById('lightbox-close').addEventListener('click', () => {
  document.getElementById('lightbox').classList.remove('active');
});

document.getElementById('lightbox').addEventListener('click', (e) => {
  if (e.target.id === 'lightbox') {
    document.getElementById('lightbox').classList.remove('active');
  }
});

document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    document.getElementById('lightbox').classList.remove('active');
  }
});
</script>

        

      <?php else: ?>
        <div class="chat-empty">
          <p>Wybierz rozmówcę, aby rozpocząć czat 💬</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
  <!-- 🖼️ Lightbox do powiększania zdjęć -->
<!-- 🖼️ Lightbox -->
<div id="lightbox">
  <span id="lightbox-close">&times;</span>
  <img src="" alt="Podgląd zdjęcia">
</div>

<script>
// 🖼️ Obsługa lightboxa
document.addEventListener('click', function(e) {
  const img = e.target.closest('.msg-image img');
  if (!img) return;

  e.preventDefault();
  const src = img.dataset.full;
  const lightbox = document.getElementById('lightbox');
  const lightboxImg = lightbox.querySelector('img');
  lightboxImg.src = src;
  lightbox.classList.add('active');
});

// Zamknięcie przyciskiem X
document.getElementById('lightbox-close').addEventListener('click', () => {
  document.getElementById('lightbox').classList.remove('active');
});

// Zamknięcie kliknięciem w tło
document.getElementById('lightbox').addEventListener('click', (e) => {
  if (e.target.id === 'lightbox') {
    e.currentTarget.classList.remove('active');
  }
});

// Zamknięcie klawiszem ESC
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    document.getElementById('lightbox').classList.remove('active');
  }
});
</script>


</main>

<?php include 'includes/footer.php'; ?>
