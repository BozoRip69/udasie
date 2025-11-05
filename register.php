<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first = trim($_POST['first_name'] ?? '');
    $last = trim($_POST['last_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirmPassword'] ?? '';

    if ($password !== $confirm) {
        header("Location: register.html?err=pass");
        exit;
    }

    
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        header("Location: register.html?err=exists");
        exit;
    }


    $avatarPath = null;

    if (!empty($_FILES['avatar']['name'])) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileName = time() . '_' . basename($_FILES['avatar']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath)) {
            $avatarPath = $targetPath;
        }
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $created = date('Y-m-d H:i:s');
    $token = bin2hex(random_bytes(32));

    $countryCode = $_POST['country_code'] ?? '+48';
    $address = trim($_POST['address'] ?? '');

    $stmt = $db->prepare("INSERT INTO users (first_name, last_name, email, country_code, phone, address, password, avatar, created_at, session_token)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$first, $last, $email, $countryCode, $phone, $address, $hash, $avatarPath ?: $defaultAvatar, $created, $token]);
        
    $_SESSION['user_email'] = $email;
    $_SESSION['session_token'] = $token;

    header("Location: dashboard.php");
    exit;
}
