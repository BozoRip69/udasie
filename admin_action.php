<?php
require 'config.php';
$user = require_login($db);

// tylko admin
if ($user['role'] !== 'admin') {
  header("Location: dashboard.php");
  exit;
}

$action = $_POST['action'] ?? '';
$id = (int)($_POST['id'] ?? 0);

if ($action === 'delete_user') {
  $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
  $stmt->execute([$id]);
} elseif ($action === 'delete_post') {
  $stmt = $db->prepare("DELETE FROM posts WHERE id = ?");
  $stmt->execute([$id]);
} elseif ($action === 'delete_user_battery') {
  $stmt = $db->prepare("DELETE FROM user_batteries WHERE id = ?");
  $stmt->execute([$id]);
} elseif ($action === 'force_update_mileage') {
  $vehicle_id = (int)$_POST['vehicle_id'];

  // 🔹 Ustaw flagę needs_update w tabeli vehicles
  $stmt = $db->prepare("UPDATE vehicles SET needs_update = 1 WHERE id = ?");
  $stmt->execute([$vehicle_id]);

  header("Location: admin.php?mileage_forced=1");
  exit;
}

header("Location: admin.php");
exit;
