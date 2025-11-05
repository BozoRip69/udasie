<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // generuj token i zapisz w DB
        $token = generateSessionToken();
        $update = $db->prepare("UPDATE users SET session_token = ? WHERE id = ?");
        $update->execute([$token, $user['id']]);

        // ustaw dane sesji
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['session_token'] = $token;

        session_regenerate_id(true);
        header("Location: dashboard.php");
        exit;
    } else {
        header("Location: login.html?err=invalid");
        exit;
    }
}

header("Location: login.html");
