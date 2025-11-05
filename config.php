<?php
session_start();

$host = "127.0.0.1";
$user = "root";
$pass = "";      // ustaw swoje dane
$dbname = "autopart";

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Błąd połączenia z bazą: " . $e->getMessage());
}

/**
 * Generuje bezpieczny token sesji
 */
function generateSessionToken() {
    return bin2hex(random_bytes(32));
}

/**
 * Sprawdza czy użytkownik jest zalogowany i czy token sesji zgadza się z bazą.
 * Jeśli nie - przekierowuje do login.html i exit.
 */
function require_login(PDO $db) {
    if (empty($_SESSION['user_email']) || empty($_SESSION['session_token'])) {
        header("Location: login.html");
        exit;
    }

    $email = $_SESSION['user_email'];
    $token = $_SESSION['session_token'];

    $stmt = $db->prepare("SELECT session_token FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // jeśli użytkownik nie istnieje lub tokeny różne -> wyloguj
    if (!$row || $row['session_token'] === null || !hash_equals($row['session_token'], $token)) {
        // zniszcz sesję i przekieruj
        session_unset();
        session_destroy();
        header("Location: login.html");
        exit;
    }

    // opcjonalnie — odśwież token sesji w PHP (nie w DB) by zmniejszyć ryzyko fixation
    session_regenerate_id(true);
}
