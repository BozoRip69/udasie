<?php
require 'config.php';
$pageTitle = "Weryfikacja kodu resetu";
$message = "";

$email = $_GET['email'] ?? '';
if (!$email) {
  header("Location: forgot_password.php");
  exit;
}

// 🧩 Obsługa formularza
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $code = trim($_POST['code']);
  $new_pass = $_POST['password'];
  $confirm = $_POST['confirm'];

  // Pobierz dane użytkownika po emailu
  $stmt = $db->prepare("SELECT id, reset_token, reset_expires FROM users WHERE email = ?");
  $stmt->execute([$email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user && $user['reset_token'] == $code && strtotime($user['reset_expires']) > time()) {
    if ($new_pass === $confirm && strlen($new_pass) >= 8) {
      $hash = password_hash($new_pass, PASSWORD_DEFAULT);
      $db->prepare("UPDATE users SET password=?, reset_token=NULL, reset_expires=NULL WHERE id=?")
         ->execute([$hash, $user['id']]);
      $message = "<div class='alert-success'>Hasło zostało zmienione! Możesz się teraz zalogować.</div>";
      header("Refresh: 3; url=login.html");
    } else {
      $message = "<div class='alert-error'>Hasła nie są zgodne lub zbyt krótkie (min. 8 znaków).</div>";
    }
  } else {
    $message = "<div class='alert-error'>Nieprawidłowy lub wygasły kod resetu.</div>";
  }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <title>Weryfikacja kodu</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body class="auth-body">
  <div class="auth-container">
    <h2>Weryfikacja kodu resetu</h2>
    <?= $message ?>
    <form method="post">
      <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
      <label>Wpisz kod, który otrzymałeś e-mailem:</label>
      <input type="text" name="code" placeholder="6-cyfrowy kod" required maxlength="6" pattern="\d{6}">
      
      <label>Nowe hasło:</label>
      <input type="password" name="password" placeholder="Nowe hasło" minlength="8" required>

      <label>Powtórz hasło:</label>
      <input type="password" name="confirm" placeholder="Powtórz hasło" minlength="8" required>

      <button type="submit">Zmień hasło</button>
    </form>
    <p style="text-align:center;margin-top:10px;">
      <a href="login.html">Powrót do logowania</a>
    </p>
  </div>
</body>
</html>
