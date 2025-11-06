<?php
require 'config.php';
$user = require_login($db);
$action = $_POST['action'] ?? null;

if ($action === 'add') {
    $brand = trim($_POST['brand']);
    $capacity = trim($_POST['capacity']);
    $voltage = trim($_POST['voltage']);
    $car_id = $_POST['car_id'] ?: null;

    $stmt = $db->prepare("INSERT INTO batteries (user_id, car_id, brand, capacity, voltage, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$user['id'], $car_id, $brand, $capacity, $voltage]);

} elseif ($action === 'delete') {
    $id = (int)$_POST['id'];
    $stmt = $db->prepare("DELETE FROM batteries WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user['id']]);
}

header("Location: batteries.php");
exit;
?>
