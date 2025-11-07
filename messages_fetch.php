<?php
require 'config.php';
$user = require_login($db);

$chat_id = isset($_GET['user']) ? (int)$_GET['user'] : 0;
if (!$chat_id) {
    echo json_encode([]);
    exit;
}

$stmt = $db->prepare("
    SELECT * FROM messages
    WHERE (sender_id = ? AND receiver_id = ?)
       OR (sender_id = ? AND receiver_id = ?)
    ORDER BY created_at ASC
");
$stmt->execute([$user['id'], $chat_id, $chat_id, $user['id']]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($messages);
?>
