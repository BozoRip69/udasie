<?php
require 'config.php';

$post_id = (int)($_GET['post_id'] ?? 0);
$stmt = $db->prepare("SELECT type, COUNT(*) AS count FROM reactions WHERE post_id=? GROUP BY type");
$stmt->execute([$post_id]);
$data = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

echo json_encode([
  'total' => array_sum($data),
  'types' => $data
]);
