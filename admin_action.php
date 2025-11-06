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
}

header("Location: admin.php");
exit;
