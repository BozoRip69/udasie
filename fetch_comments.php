<?php
require 'config.php';
require_login($db);

$post_id = (int)($_GET['post_id'] ?? 0);
if (!$post_id) {
    header('Content-Type: application/json');
    echo json_encode([]);
    exit;
}

$stmt = $db->prepare("
  SELECT c.content, c.created_at, u.first_name, u.last_name, u.avatar
  FROM comments c
  JOIN users u ON u.id = c.user_id
  WHERE c.post_id = ?
  ORDER BY c.created_at ASC
");
$stmt->execute([$post_id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($comments);
