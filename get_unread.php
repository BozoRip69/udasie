<?php
require 'config.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user']['id'])) {
  echo json_encode(['messages' => 0, 'notifications' => 0]);
  exit;
}

$user_id = $_SESSION['user']['id'];

// 🟢 Nieprzeczytane wiadomości
$stmt = $db->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0");
$stmt->execute([$user_id]);
$msg_count = $stmt->fetchColumn();

// 🟢 Powiadomienia (na razie puste)
$notif_count = 0;

echo json_encode(['messages' => $msg_count, 'notifications' => $notif_count]);
