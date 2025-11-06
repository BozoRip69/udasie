<?php
require 'config.php';
$pageTitle = "Nowe hasło";
$message = "";

$token = $_GET['token'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $token = $_POST['token'];
  $password = $_POST['password'];
  $confirm = $_POST['confirm'];

  $stmt = $db->prepare("SELECT id FROM users WHERE reset_token=? AND reset_expires > NOW()");
  $stmt->execute([$token]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user && $password === $confirm && strlen($password) >= 6) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $db->prepare("UPDATE users SET password=?, reset_token=NULL, reset_expires=NULL WHERE id=?")
       ->execute([$hash, $user['id']]);
    $message = "<div class='alert-success'>Hasło zostało zmienione! Możesz się zalogować.</div>";
  } else {
    $message = "<div class='alert-error'>Nieprawidłowy token lub hasła nie są zgodne.</div>";
  }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <title>Nowe hasło</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body class="auth-body">
  <div class="auth-container">
    <h2>Ustaw nowe hasło</h2>
    <?= $message ?>
    <form method="post">
      <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
      <input type="password" name="password" placeholder="Nowe hasło" required minlength="6">
      <input type="password" name="confirm" placeholder="Powtórz hasło" required minlength="6">
      <button type="submit">Zmień hasło</button>
    </form>
    <p style="margin-top:10px;text-align:center;">
      <a href="login.html">Powrót do logowania</a>
    </p>
  </div>
</body>
</html>
