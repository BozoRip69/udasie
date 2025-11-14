<?php
require 'config.php';
$message = "";

$email = $_GET['email'] ?? '';
if (!$email) {
    header("Location: login.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code']);

    // Pobranie użytkownika
    $stmt = $db->prepare("SELECT id, verify_code, verify_expires FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $user['verify_code'] == $code && strtotime($user['verify_expires']) > time()) {

        // Ustawiamy verified=1
        $db->prepare("UPDATE users SET verified = 1, verify_code = NULL, verify_expires = NULL WHERE id = ?")
           ->execute([$user['id']]);

        // Logowanie użytkownika
        $_SESSION['user_email'] = $email;

        // Przekierowanie na dashboard
        header("Location: dashboard.php");
        exit;

    } else {
        $message = "<div class='alert-error'>Nieprawidłowy lub wygasły kod.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <title>Weryfikacja konta</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body class="auth-body">
  <div class="auth-container">
    <h2>Potwierdź swój email</h2>
    <?= $message ?>
    <p>Wpisz 6-cyfrowy kod wysłany na adres <b><?= htmlspecialchars($email) ?></b></p>

    <form method="post">
      <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">

      <label>Kod weryfikacyjny:</label>
      <input type="text" name="code" maxlength="6" pattern="\d{6}" required placeholder="123456">

      <button type="submit">Potwierdź</button>
    </form>

    <p style="text-align:center;margin-top:10px;">
      <a href="login.html">Powrót</a>
    </p>
  </div>
</body>
</html>
