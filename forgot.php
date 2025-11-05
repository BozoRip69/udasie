<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "<p style='color:white;text-align:center;margin-top:50px;'>Nie znaleziono użytkownika o tym adresie e-mail.</p>";
        exit;
    }

    $token = bin2hex(random_bytes(32));
    $expires = date("Y-m-d H:i:s", time() + 3600);

    $update = $db->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
    $update->execute([$token, $expires, $email]);

    $resetLink = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset.html?token=$token";

    echo "<body style='background:#0046ad;color:white;text-align:center;font-family:Segoe UI;padding:40px;'>
          <h2>Link do resetowania hasła został wygenerowany ✅</h2>
          <p>Skopiuj i otwórz w przeglądarce poniższy link:</p>
          <p style='background:rgba(255,255,255,0.1);padding:15px;border-radius:8px;word-break:break-all;'>$resetLink</p>
          <a href='login.html' style='color:#fff;text-decoration:underline;'>Powrót do logowania</a>
          </body>";
}
