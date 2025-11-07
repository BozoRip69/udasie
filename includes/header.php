<?php
if (!isset($pageTitle)) $pageTitle = "AutoPart";

// Liczba nieprzeczytanych wiadomości
$stmt = $db->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id=? AND is_read=0");
$stmt->execute([$user['id']]);
$unread_msgs = (int)$stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<nav>
  <img src="logo.png" alt="AutoPart Battery" class="logo">

  <div class="nav-links">
    <a href="dashboard.php"><i class="fa-solid fa-gauge"></i> Panel</a>
    <a href="browse.php"><i class="fa-solid fa-newspaper"></i> Przeglądanie</a>
    <a href="garage.php"><i class="fa-solid fa-car"></i> Garaż</a>

    <a href="messages.php" class="nav-msg">
      <i class="fa-solid fa-envelope"></i> Wiadomości
      <?php if ($unread_msgs > 0): ?>
        <span id="msg-counter" class="msg-badge"><?= $unread_msgs ?></span>
      <?php else: ?>
        <span id="msg-counter" class="msg-badge" style="display:none;"></span>
      <?php endif; ?>
    </a>

    <a href="notifications.php"><i class="fa-solid fa-bell"></i> Powiadomienia</a>
    <a href="users.php"><i class="fa-solid fa-users"></i> Użytkownicy</a>

    <?php if (isset($user) && $user['role'] === 'admin'): ?>
      <a href="admin.php" class="admin-link">
        <i class="fa-solid fa-shield-halved"></i> Admin
      </a>
    <?php endif; ?>

    <a href="account.php" class="account-link">
      <i class="fa-solid fa-gear"></i> Konto
      <span class="role-badge <?= $user['role'] ?>">
        <?= $user['role'] === 'admin' ? 'Administrator' : 'Użytkownik' ?>
      </span>
    </a>

    <button id="theme-toggle" class="theme-toggle" title="Zmień tryb">
      <i class="fa-solid fa-moon"></i>
    </button>

    <form method="post" action="logout.php" style="display:inline;">
      <button type="submit" class="nav-logout">
        <i class="fa-solid fa-right-from-bracket"></i> Wyloguj
      </button>
    </form>
  </div>
</nav>

<main>

<script>
// 🔔 Live sprawdzanie wiadomości + dźwięk powiadomienia (z pamięcią)
const audioPing = new Audio('assets/sounds/ping.mp3');

// Pobierz poprzednią wartość z localStorage
let lastTotal = parseInt(localStorage.getItem('lastUnreadTotal') || '0');

function updateMessageCounter() {
  fetch('check_messages.php')
    .then(r => r.json())
    .then(data => {
      let total = 0;

      if (Array.isArray(data)) {
        data.forEach(item => total += item.unread_count || 0);
      } else if (data.total !== undefined) {
        total = data.total;
      }

      const badge = document.getElementById('msg-counter');

      if (total > 0) {
        if (badge) {
          badge.textContent = total;
          badge.style.display = 'inline-block';
        } else {
          const newBadge = document.createElement('span');
          newBadge.id = 'msg-counter';
          newBadge.className = 'msg-badge';
          newBadge.textContent = total;
          document.querySelector('.nav-msg').appendChild(newBadge);
        }
      } else if (badge) {
        badge.style.display = 'none';
      }

      // 🔊 Odtwórz ping tylko, gdy faktycznie przybyła nowa wiadomość
      if (total > lastTotal) {
        try {
          audioPing.currentTime = 0;
          audioPing.play().catch(() => {});
        } catch (e) {
          console.warn('Nie udało się odtworzyć dźwięku:', e);
        }
      }

      // Zapisz nową wartość w localStorage
      localStorage.setItem('lastUnreadTotal', total);

      // Zaktualizuj zmienną w pamięci
      lastTotal = total;
    })
    .catch(err => console.error('Błąd aktualizacji wiadomości:', err));
}

// 🔁 sprawdzaj co 3 sekundy
setInterval(updateMessageCounter, 3000);
updateMessageCounter();
</script>


