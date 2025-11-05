<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // pobierz usera
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // generuj nowy token sesji
        $token = generateSessionToken();

        // zaktualizuj last_login i session_token
        $update = $db->prepare("UPDATE users SET last_login = NOW(), session_token = ? WHERE id = ?");
        $update->execute([$token, $user['id']]);

        // ustaw sesję
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
