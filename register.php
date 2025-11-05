<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    $stmt->execute([$email, $hash]);

    // pobierz id nowego usera
    $userId = $db->lastInsertId();

    // wygeneruj token i zapisz w DB
    $token = generateSessionToken();
    $update = $db->prepare("UPDATE users SET session_token = ? WHERE id = ?");
    $update->execute([$token, $userId]);

    // ustaw sesję
    $_SESSION['user_email'] = $email;
    $_SESSION['session_token'] = $token;
    session_regenerate_id(true);

    header("Location: dashboard.php");
    exit;
}

header("Location: register.html");
