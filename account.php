<?php
require 'config.php';

// wymaga funkcji require_login(PDO $db) w config.php
require_login($db);

// bieżący użytkownik (zabezpieczony)
$userEmail = $_SESSION['user_email'] ?? '';

$message = ''; // HTML komunikatu (error/success) wyświetlany na stronie

// obsługa zmiany hasła
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    // podstawowa walidacja
    if (trim($current) === '' || trim($new) === '' || trim($confirm) === '') {
        $message = "<div class='error'>❌ Wypełnij wszystkie pola.</div>";
    } elseif ($new !== $confirm) {
        $message = "<div class='error'>❌ Nowe hasła nie są takie same.</div>";
    } elseif (strlen($new) < 6) {
        $message = "<div class='error'>❌ Nowe hasło musi mieć co najmniej 6 znaków.</div>";
    } else {
        // pobierz użytkownika z bazy
        $stmt = $db->prepare("SELECT id, password FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$userEmail]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            // niespodziewane - brak użytkownika
            $message = "<div class='error'>❌ Użytkownik nie istnieje.</div>";
        } else {
            // sprawdź aktualne hasło
            if (!password_verify($current, $data['password'])) {
                $message = "<div class='error'>❌ Błędne aktualne hasło.</div>";
            } else {
                // wszystko OK — zaktualizuj hasło i unieważnij session_token
                $newHash = password_hash($new, PASSWORD_DEFAULT);
                $update = $db->prepare("UPDATE users SET password = ?, session_token = NULL WHERE id = ?");
                $update->execute([$newHash, $data['id']]);

                // zniszcz bieżącą sesję (wyloguje też inne urządzenia, bo token w DB został usunięty)
                session_unset();
                session_destroy();

                // przekieruj do logowania z komunikatem
                header("Location: login.html?msg=pass_changed");

                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Ustawienia konta - AutoPart Battery</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Segoe UI", Arial, sans-serif; }
    body {
      background: linear-gradient(180deg, #0046ad 60%, #003b93 100%);
      color: white;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    nav {
      background: rgba(255,255,255,0.1);
      backdrop-filter: blur(6px);
      display:flex;
      justify-content:space-between;
      align-items:center;
      padding:15px 40px;
      border-bottom:1px solid rgba(255,255,255,0.15);
      box-shadow:0 4px 10px rgba(0,0,0,0.2);
    }
    nav img { height:45px; }
    .nav-links { display:flex; align-items:center; gap:20px; }
    .nav-links a { color:#fff; text-decoration:none; font-weight:500; transition:0.2s; }
    .nav-links a:hover { color:#dfe9ff; }
    .logout-btn {
      background: white;
      color: #0046ad;
      border: none;
      border-radius: 8px;
      padding: 8px 14px;
      font-size: 0.9rem;
      cursor: pointer;
    }
    main {
      flex:1;
      display:flex;
      justify-content:center;
      align-items:flex-start;
      padding:60px 20px;
    }
    .settings-container {
      background: rgba(255,255,255,0.1);
      border:1px solid rgba(255,255,255,0.2);
      backdrop-filter: blur(8px);
      border-radius:16px;
      padding:36px 44px;
      width:100%;
      max-width:640px;
      text-align:center;
      box-shadow:0 8px 25px rgba(0,0,0,0.3);
      animation: fadeIn 0.8s ease forwards;
    }
    .settings-container h1 { font-size:1.8rem; margin-bottom:16px; }
    .settings-container p { margin-bottom:20px; opacity:0.9; }
    form { text-align:left; margin-top:10px; }
    label { display:block; font-size:0.9rem; margin-bottom:6px; color:#dfe9ff; }
    input {
      width:100%;
      padding:12px;
      border:none;
      border-radius:8px;
      background:rgba(255,255,255,0.9);
      font-size:1rem;
      color:#003b93;
      margin-bottom:14px;
    }
    input:focus { outline:none; box-shadow:0 0 0 3px rgba(255,255,255,0.3); }
    .save-btn {
      width:100%;
      background:white;
      color:#0046ad;
      border:none;
      border-radius:8px;
      padding:12px;
      font-size:1rem;
      cursor:pointer;
    }
    .save-btn:hover { background:#dfe9ff; }
    .error, .success {
      margin-top:14px;
      padding:12px;
      border-radius:8px;
      font-size:0.95rem;
      animation: fadeIn 0.4s ease;
    }
    .error { background:rgba(255,0,0,0.12); color:#ffdede; }
    .success { background:rgba(0,255,0,0.12); color:#baffba; }
    footer { text-align:center; padding:20px; font-size:0.85rem; opacity:0.75; }
    @keyframes fadeIn { from{opacity:0;transform:translateY(10px);} to{opacity:1;transform:none;} }
    @media (max-width:600px) {
      nav { padding:15px 20px; flex-direction:column; gap:10px; }
      .settings-container { padding:22px; }
    }
  </style>
</head>
<body>

  <nav>
    <img src="logo.png" alt="AutoPart Battery">
    <div class="nav-links">
      <a href="dashboard.php"><i class="fa-solid fa-gauge"></i> Panel główny</a>
      <a href="account.php"><i class="fa-solid fa-gear"></i> Ustawienia konta</a>
      <form method="post" action="logout.php" style="display:inline;">
        <button type="submit" class="logout-btn">Wyloguj</button>
      </form>
    </div>
  </nav>

  <main>
    <div class="settings-container">
      <h1>Ustawienia konta</h1>
      <p><strong>Zalogowano jako:</strong> <?php echo htmlspecialchars($userEmail); ?></p>

      <form method="post" autocomplete="off">
        <label for="current_password">Aktualne hasło</label>
        <input type="password" id="current_password" name="current_password" required>

        <label for="new_password">Nowe hasło</label>
        <input type="password" id="new_password" name="new_password" required>

        <label for="confirm_password">Powtórz nowe hasło</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <button type="submit" class="save-btn">Zmień hasło</button>
      </form>

      <?php
        if ($message) {
            echo $message;
        }
      ?>

      <div style="margin-top:18px; text-align:left; font-size:0.95rem; opacity:0.9;">
        <p><strong>Uwaga:</strong> Po zmianie hasła zostaniesz wylogowany(-a) ze wszystkich urządzeń.</p>
      </div>
    </div>
  </main>

  <footer>
    © 2025 AutoPart Battery — Panel użytkownika
  </footer>

</body>
</html>
