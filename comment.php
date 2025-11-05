<?php
require 'config.php';
require_login($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = (int)($_POST['post_id'] ?? 0);
    $content = trim($_POST['content'] ?? '');
    if (!$post_id || $content === '') exit;

    $email = $_SESSION['user_email'];
    $u = $db->prepare("SELECT id FROM users WHERE email = ?");
    $u->execute([$email]);
    $user = $u->fetch(PDO::FETCH_ASSOC);
    if (!$user) exit;

    $stmt = $db->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
    $stmt->execute([$post_id, $user['id'], $content]);
}
