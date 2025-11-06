<?php
require 'config.php';
require_login($db);
$user = require_login($db);
$other = (int)($_GET['user_id'] ?? 0);

$stmt = $db->prepare("
  SELECT * FROM messages 
  WHERE (sender_id=? AND receiver_id=?) OR (sender_id=? AND receiver_id=?) 
  ORDER BY created_at ASC
");
$stmt->execute([$user['id'], $other, $other, $user['id']]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
