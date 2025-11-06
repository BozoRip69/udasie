<?php
require 'config.php';
$pageTitle = "Reset hasła";
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email']);
  $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
  $stmt->execute([$email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user) {
    // Wygeneruj token resetu
    $token = bin2hex(random_bytes(16));
    $db->prepare("UPDATE users SET reset_token=?, reset_expires=DATE_ADD(NOW(), INTERVAL 15 MINUTE) WHERE id=?")
       ->execute([$token, $user['id']]);

    // Link resetu
    
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST']; // np. localhost
    $base   = rtrim(dirname($_SERVER['REQUEST_URI']), '/\\'); // np. /udasie
    $resetLink = $scheme.'://'.$host.$base.'/reset_password.php?token='.$token;
    $message = "<div class='alert-success'>Link do resetu hasła (tymczasowy test):<br><a href='$resetLink'>$resetLink</a></div>";
  } else {
    $message = "<div class='alert-error'>Nie znaleziono użytkownika o tym adresie e-mail.</div>";
  }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <title>Odzyskiwanie hasła</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body class="auth-body">
  <div class="auth-container">
    <h2>Odzyskiwanie hasła</h2>
    <?= $message ?>
    <form method="post">
      <input type="email" name="email" placeholder="Podaj adres e-mail" required>
      <button type="submit">Wyślij link resetujący</button>
    </form>
    <p style="margin-top:10px;text-align:center;">
      <a href="login.html">Powrót do logowania</a>
    </p>
  </div>
</body>
</html>
