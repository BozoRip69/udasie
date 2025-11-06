<?php
require 'config.php';
$user = require_login($db);

$stmt = $db->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0");
$stmt->execute([$user['id']]);
$total = (int)$stmt->fetchColumn();

header('Content-Type: application/json');
echo json_encode(['total' => $total]);
