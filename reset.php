<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($new !== $confirm) {
        exit("<body style='background:#0046ad;color:white;text-align:center;padding:40px;'>
              <h2>❌ Hasła nie są takie same</h2><a href='login.html' style='color:#fff;'>Powrót</a></body>");
    }

    $stmt = $db->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        exit("<body style='background:#0046ad;color:white;text-align:center;padding:40px;'>
              <h2>❌ Link resetujący wygasł lub jest nieprawidłowy</h2><a href='login.html' style='color:#fff;'>Powrót</a></body>");
    }

    // ustaw nowe hasło
    $hash = password_hash($new, PASSWORD_DEFAULT);
    $update = $db->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
    $update->execute([$hash, $user['id']]);

    echo "<body style='background:#0046ad;color:white;text-align:center;padding:40px;'>
          <h2>✅ Hasło zostało zmienione pomyślnie!</h2>
          <a href='login.html' style='color:#fff;'>Zaloguj się</a></body>";
}
