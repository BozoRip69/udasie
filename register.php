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

    // sprawdź, czy email już istnieje
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        header("Location: register.html?err=exists");
        exit;
    }

    // 🔒 hashowanie hasła — to kluczowe
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $db->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    $stmt->execute([$email, $hash]);

    $_SESSION['user_email'] = $email;
    header("Location: dashboard.php");
    exit;
}

header("Location: register.html");
