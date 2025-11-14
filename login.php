<?php
require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (!empty($email) && !empty($password)) {
        $stmt = $db->prepare("SELECT id, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION["user_id"] = $user['id'];
            $_SESSION["user_email"] = $email;
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Nieprawidłowy e-mail lub hasło.";
        }
    } else {
        $error = "Wypełnij wszystkie pola.";
    }
}

?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Logowanie - AutoPart</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body class="auth-body">
<div class="auth-body">
  <div class="auth-container">
    <h2>Logowanie</h2>

    <?php if (!empty($error)): ?>
        <p style="color: red; text-align:center;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form action="login.php" method="post">
      <input type="email" name="email" placeholder="Adres e-mail" required>
      <input type="password" name="password" placeholder="Hasło" required>
      <button type="submit">Zaloguj</button>
    </form>

    <p style="margin-top: 10px; text-align:center;">
      <a href="register.php">Nie masz konta? Zarejestruj się</a><br>
      <a href="forgot_password.php" class="forgot-link">Nie pamiętasz hasła?</a>
    </p>
  </div>
</div>
</body>
</html>
