<?php
session_start();

$host = "localhost";     // lub inny, jeśli używasz zewnętrznego serwera
$user = "root";          // Twój login MySQL
$pass = "";              // Twoje hasło MySQL
$dbname = "autopart";

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Błąd połączenia z bazą: " . $e->getMessage());
}
