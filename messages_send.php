<?php
require 'config.php';
$user = require_login($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $receiver = (int)$_POST['receiver_id'];
  $content = trim($_POST['content']);
  if ($content && $receiver) {
    $stmt = $db->prepare("INSERT INTO messages (sender_id, receiver_id, content, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$user['id'], $receiver, $content]);

    // Dodaj powiadomienie
    $db->prepare("INSERT INTO notifications (user_id, type, content, link, created_at)
      VALUES (?, 'message', ?, 'messages.php?user_id=$user[id]', NOW())")
      ->execute([$receiver, $user['first_name'].' wysłał Ci wiadomość.']);
  }
}
