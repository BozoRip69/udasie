<?php
require 'config.php';
require_login($db);
$post_id = (int)($_GET['post_id'] ?? 0);
$stmt = $db->prepare("SELECT c.content, c.created_at, u.first_name, u.last_name FROM comments c JOIN users u ON u.id=c.user_id WHERE c.post_id=? ORDER BY c.created_at ASC");
$stmt->execute([$post_id]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
