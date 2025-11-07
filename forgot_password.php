<?php
require 'config.php';
$pageTitle = "Reset hasła";
$message = "";

// 📦 PHPMailer (Composer)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email']);

  $stmt = $db->prepare("SELECT id, first_name FROM users WHERE email = ?");
  $stmt->execute([$email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user) {
    // 🔢 Wygeneruj kod resetu
    $code = random_int(100000, 999999);

    // 🕒 Zapisz w bazie
    $db->prepare("UPDATE users SET reset_token=?, reset_expires=DATE_ADD(NOW(), INTERVAL 15 MINUTE) WHERE id=?")
       ->execute([$code, $user['id']]);

    // ✉️ Wysyłka maila
    $mail = new PHPMailer(true);
    try {
      $mail->isSMTP();
      $mail->Host = 'serwer2033253.home.pl'; // 📡 serwer SMTP
      $mail->SMTPAuth = true;
      $mail->Username = 'noreply@autopart.pl'; // 📧 Twój adres
      $mail->Password = 'oR/5FsNI!M<G'; // 🔑 Twoje hasło
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
      $mail->Port = 465;

      $mail->setFrom('noreply@autopart.pl', 'AutoPart');
      $mail->addAddress($email, $user['first_name']);
      $mail->isHTML(true);
      $mail->Subject = 'Kod resetu hasla - AutoPart';
      $mail->Body = "
        <h2>Witaj {$user['first_name']},</h2>
        <p>Twój kod do resetu hasła to:</p>
        <div style='font-size:24px;font-weight:bold;color:#007bff;'>$code</div>
        <p>Kod jest ważny przez 15 minut.</p>
        <hr>
        <small>Jeśli to nie Ty inicjowałeś reset hasła, zignoruj tę wiadomość.</small>
      ";

      $mail->send();
      $message = "<div class='alert-success'>Kod resetu został wysłany na adres <b>$email</b>.</div>";

      header("Location: verify_code.php?email=" . urlencode($email));
      exit;

    } catch (Exception $e) {
      $message = "<div class='alert-error'>Błąd wysyłania wiadomości: {$mail->ErrorInfo}</div>";
    }

  } else {
    // Nie zdradzaj, że e-mail nie istnieje
    $message = "<div class='alert-success'>Jeśli podany e-mail istnieje, kod resetu został wysłany.</div>";
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
      <button type="submit">Wyślij kod resetujący</button>
    </form>
    <p style="margin-top:10px;text-align:center;">
      <a href="login.html">Powrót do logowania</a>
    </p>
  </div>
</body>
</html>
