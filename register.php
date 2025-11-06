<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first = trim($_POST['first_name']);
    $last = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $countryCode = $_POST['country_code'];
    $phone = preg_replace('/\D/', '', $_POST['phone']);
    $address = trim($_POST['address']);
    $pass = $_POST['password'];
    $confirm = $_POST['confirm'];

    if ($pass !== $confirm) {
        die("<script>alert('Hasła nie są takie same!');history.back();</script>");
    }

    // sprawdź czy użytkownik istnieje
    $check = $db->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$email]);
    if ($check->fetch()) {
        die("<script>alert('Użytkownik o tym adresie e-mail już istnieje.');history.back();</script>");
    }

    // haszuj hasło
    $hash = password_hash($pass, PASSWORD_BCRYPT);
    $created = date('Y-m-d H:i:s');
    $token = generateSessionToken();

    // avatar
    $avatarPath = 'default-avatar.png';
    if (!empty($_FILES['avatar']['name'])) {
        $uploadDir = 'uploads/avatars/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $fileName = time().'_'.basename($_FILES['avatar']['name']);
        $target = $uploadDir.$fileName;
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target)) {
            $avatarPath = $target;
        }
    }

    // dodaj użytkownika
    $stmt = $db->prepare("INSERT INTO users 
        (first_name, last_name, email, country_code, phone, address, password, avatar, created_at, session_token)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$first, $last, $email, $countryCode, $phone, $address, $hash, $avatarPath, $created, $token]);

    $_SESSION['user_email'] = $email;
    header("Location: dashboard.php");
    exit;
}
?>
