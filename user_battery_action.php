<?php
require 'config.php';
$user = require_login($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  if ($action === 'add') {
    $battery_id = $_POST['battery_id'];
    $vehicle_id = $_POST['vehicle_id'] ?: null;
    $place = $_POST['purchase_place'] ?: null;
    $date = $_POST['purchase_date'] ?: null;
    $notes = $_POST['notes'] ?: null;

    $uploadDir = 'uploads/user_batteries/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $battery_image = null;
    $receipt_image = null;

    if (!empty($_FILES['battery_image']['tmp_name'])) {
      $file = $uploadDir . uniqid('bat_') . '.' . pathinfo($_FILES['battery_image']['name'], PATHINFO_EXTENSION);
      move_uploaded_file($_FILES['battery_image']['tmp_name'], $file);
      $battery_image = $file;
    }

    if (!empty($_FILES['receipt_image']['tmp_name'])) {
      $file = $uploadDir . uniqid('rec_') . '.' . pathinfo($_FILES['receipt_image']['name'], PATHINFO_EXTENSION);
      move_uploaded_file($_FILES['receipt_image']['tmp_name'], $file);
      $receipt_image = $file;
    }

    $stmt = $db->prepare("INSERT INTO user_batteries (user_id, vehicle_id, battery_id, purchase_place, purchase_date, battery_image, receipt_image, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user['id'], $vehicle_id, $battery_id, $place, $date, $battery_image, $receipt_image, $notes]);

    header("Location: my_batteries.php");
    exit;
  }

  if ($action === 'delete' && !empty($_POST['id'])) {
    $stmt = $db->prepare("DELETE FROM user_batteries WHERE id = ? AND user_id = ?");
    $stmt->execute([$_POST['id'], $user['id']]);
    header("Location: my_batteries.php");
    exit;
  }
}
?>
