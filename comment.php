<?php
require 'config.php';
require_login($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $post_id = (int)$_POST['post_id'];
  $content = trim($_POST['content']);
  if ($content) {
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$_SESSION['user_email']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $db->prepare("INSERT INTO comments (post_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())")
      ->execute([$post_id, $user['id'], $content]);
  }
}
