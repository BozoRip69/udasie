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
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// policz sumę nieprzeczytanych
$total = 0;
foreach ($rows as $r) $total += $r['unread_count'];

header('Content-Type: application/json');
echo json_encode([
  'total' => $total,
  'by_user' => $rows
]);
?>
