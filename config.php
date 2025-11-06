<?php
// ===============================
// AutoPart - konfiguracja bazy
// ===============================
session_start();

$host = 'localhost';
$dbname = 'autopart';
$username = 'root';
$password = ''; // <- ustaw swoje hasło MySQL

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Błąd połączenia z bazą danych: " . $e->getMessage());
}

// ==================================
// Funkcje sesji i bezpieczeństwa
// ==================================
function require_login($db) {
    if (!isset($_SESSION['user_email'])) {
        header("Location: login.php");
        exit;
    }
    $email = $_SESSION['user_email'];
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        session_destroy();
        header("Location: login.php");
        exit;
    }
    return $user;
}

function generateSessionToken() {
    return bin2hex(random_bytes(32));
}
?>
