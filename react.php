<?php
require 'config.php';
require_login($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $post_id = (int)$_POST['post_id'];
  $type = $_POST['type'];

  $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
  $stmt->execute([$_SESSION['user_email']]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  $check = $db->prepare("SELECT * FROM reactions WHERE post_id=? AND user_id=?");
  $check->execute([$post_id, $user['id']]);
  if ($check->fetch()) {
    $db->prepare("DELETE FROM reactions WHERE post_id=? AND user_id=?")->execute([$post_id, $user['id']]);
  } else {
    $db->prepare("INSERT INTO reactions (post_id, user_id, type, created_at) VALUES (?, ?, ?, NOW())")
      ->execute([$post_id, $user['id'], $type]);
  }
  $db->prepare("
  INSERT INTO notifications (user_id, type, content, link, created_at)
  SELECT p.user_id, 'like', CONCAT(:name, ' zareagował(a) na Twój post'), CONCAT('browse.php#post-', p.id), NOW()
  FROM posts p WHERE p.id = :pid
")->execute([':pid'=>$post_id, ':name'=>$user['first_name']]);
}
