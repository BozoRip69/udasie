<?php
require 'config.php';
if (empty($_SESSION['user_email'])) {
    header("Location: login.html");
    exit;
}

$user = htmlspecialchars($_SESSION['user_email']);
$message = "";

// obsługa zmiany hasła
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($new !== $confirm) {
        $message = "<div class='error'>❌ Nowe hasła nie są takie same.</div>";
    } else {
        // sprawdź aktualne hasło
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$user]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data && password_verify($current, $data['password'])) {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            $update = $db->prepare("UPDATE users SET password = ? WHERE email = ?");
            $update->execute([$hash, $user]);
            $message = "<div class='success'>✅ Hasło zostało pomyślnie zmienione!</div>";
        } else {
            $message = "<div class='error'>❌ Błędne aktualne hasło.</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(6px);
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 40px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.15);
      box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }

    nav img { height: 45px; }

    .nav-links { display: flex; align-items: center; gap: 25px; }

    .nav-links a {
      color: #fff;
      text-decoration: none;
      font-weight: 500;
      transition: 0.3s;
    }

    .nav-links a:hover { color: #dfe9ff; }

    .logout-btn {
      background: white;
      color: #0046ad;
      border: none;
      border-radius: 8px;
      padding: 8px 16px;
      font-size: 0.9rem;
      cursor: pointer;
      transition: 0.3s;
    }

    .logout-btn:hover { background: #dfe9ff; }

    main {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      padding: 60px 20px;
    }

    .settings-container {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(8px);
      border-radius: 16px;
      padding: 40px 50px;
      width: 100%;
      max-width: 600px;
      text-align: center;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
      animation: fadeIn 0.8s ease forwards;
    }

    .settings-container h1 { font-size: 1.8rem; margin-bottom: 25px; }

    .settings-container p { margin-bottom: 25px; opacity: 0.85; }

    form { text-align: left; }

    label {
      display: block;
      font-size: 0.9rem;
      margin-bottom: 5px;
      color: #dfe9ff;
    }

    input {
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 8px;
      background: rgba(255, 255, 255, 0.9);
      font-size: 1rem;
      color: #003b93;
      margin-bottom: 15px;
    }

    input:focus {
      outline: none;
      box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.3);
    }

    .save-btn {
      width: 100%;
      background: white;
      color: #0046ad;
      border: none;
      border-radius: 8px;
      padding: 12px;
      font-size: 1rem;
      cursor: pointer;
      transition: 0.3s;
    }

    .save-btn:hover { background: #dfe9ff; }

    .error, .success {
      margin-top: 15px;
      padding: 12px;
      border-radius: 8px;
      font-size: 0.95rem;
      animation: fadeIn 0.5s ease;
    }

    .error {
      background: rgba(255, 0, 0, 0.15);
      color: #ffbaba;
    }

    .success {
      background: rgba(0, 255, 0, 0.15);
      color: #baffba;
    }

    footer {
      text-align: center;
      padding: 20px;
      font-size: 0.85rem;
      opacity: 0.7;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 600px) {
      nav { padding: 15px 20px; flex-direction: column; gap: 10px; }
      .nav-links { flex-wrap: wrap; justify-content: center; gap: 15px; }
      .settings-container { padding: 25px 20px; }
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
      <p><strong>Zalogowano jako:</strong> <?php echo $user; ?></p>

      <form method="post">
        <label for="current_password">Aktualne hasło</label>
        <input type="password" id="current_password" name="current_password" required>

        <label for="new_password">Nowe hasło</label>
        <input type="password" id="new_password" name="new_password" required>

        <label for="confirm_password">Powtórz nowe hasło</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <button type="submit" class="save-btn">Zapisz zmiany</button>
      </form>

      <?php echo $message; ?>
    </div>
  </main>

  <footer>
    © 2025 AutoPart Battery — Panel użytkownika
  </footer>

</body>
</html>
