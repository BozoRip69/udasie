<?php
require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first_name    = trim($_POST["first_name"]);
    $last_name     = trim($_POST["last_name"]);
    $email         = trim($_POST["email"]);
    $confirm_email = trim($_POST["confirm_email"]);
    $country_code  = trim($_POST["country_code"]);
    $phone         = trim($_POST["phone"]);
    $password      = trim($_POST["password"]);
    $confirm_pass  = trim($_POST["confirm_pass"]);

    // Walidacja pól
    if (
        empty($first_name) || empty($last_name) ||
        empty($email) || empty($confirm_email) ||
        empty($password) || empty($confirm_pass)
    ) {
        $error = "Wypełnij wszystkie wymagane pola.";
    } elseif ($email !== $confirm_email) {
        $error = "Adresy e-mail muszą być identyczne.";
    } elseif ($password !== $confirm_pass) {
        $error = "Hasła muszą być identyczne.";
    } else {
        // Sprawdź, czy e-mail już istnieje
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Konto z tym adresem e-mail już istnieje.";
        } else {
            // Zapis nowego użytkownika
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (first_name, last_name, email, country_code, phone, password) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$first_name, $last_name, $email, $country_code, $phone, $hashed_password]);
            $success = "Konto zostało utworzone! Możesz się teraz zalogować.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Rejestracja - AutoPart</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body class="auth-body">
<div class="auth-container">
  <h2>Załóż konto</h2>

  <?php if (!empty($error)): ?>
      <p style="color: red; text-align:center;"><?php echo htmlspecialchars($error); ?></p>
  <?php elseif (!empty($success)): ?>
      <p style="color: green; text-align:center;"><?php echo htmlspecialchars($success); ?></p>
  <?php endif; ?>

  <form action="register.php" method="post" onsubmit="return validateForm()">
    <label>Imię</label>
    <input type="text" name="first_name" required>

    <label>Nazwisko</label>
    <input type="text" name="last_name" required>

    <label>Adres e-mail</label>
    <input type="email" id="email" name="email" required>

    <label>Powtórz adres e-mail</label>
    <input type="email" id="confirm_email" name="confirm_email" required>

    <label>Numer telefonu</label>
    <div class="phone-input-wrapper">
      <select name="country_code" required>
        <option value="+48" selected>🇵🇱 +48</option>
        <option value="+49">🇩🇪 +49</option>
        <option value="+44">🇬🇧 +44</option>
        <option value="+420">🇨🇿 +420</option>
        <option value="+421">🇸🇰 +421</option>
        <option value="+33">🇫🇷 +33</option>
        <option value="+39">🇮🇹 +39</option>
        <option value="+1">🇺🇸 +1</option>
      </select>
      <input type="text" id="phone" name="phone" placeholder="123 456 789" maxlength="11" required>
    </div>

    <label>Hasło</label>
    <input type="password" id="password" name="password" required>

    <label>Powtórz hasło</label>
    <input type="password" id="confirm_pass" name="confirm_pass" required>

    <button type="submit">Zarejestruj</button>
    <p>Masz już konto? <a href="login.php">Zaloguj się</a></p>
  </form>
</div>

<script>
// Walidacja front-endowa
function validateForm() {
  const email = document.getElementById("email").value;
  const confirmEmail = document.getElementById("confirm_email").value;
  const pass = document.getElementById("password").value;
  const confirmPass = document.getElementById("confirm_pass").value;

  if (email !== confirmEmail) {
    alert("Adresy e-mail muszą być identyczne!");
    return false;
  }

  if (pass !== confirmPass) {
    alert("Hasła muszą być identyczne!");
    return false;
  }

  return true;
}
</script>

</body>
</html>
