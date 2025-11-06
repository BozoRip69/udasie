<?php
require 'config.php';
$user = require_login($db);

$stmt = $db->prepare("
  SELECT sender_id AS user_id, COUNT(*) AS unread_count
  FROM messages
  WHERE receiver_id = ? AND is_read = 0
  GROUP BY sender_id
");
$stmt->execute([$user['id']]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($result);
