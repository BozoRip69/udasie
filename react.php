<?php
require 'config.php';
require_login($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = (int)$_POST['post_id'];
    $type = $_POST['type'] ?? 'like';
    $email = $_SESSION['user_email'];

    $u = $db->prepare("SELECT id FROM users WHERE email = ?");
    $u->execute([$email]);
    $user = $u->fetch(PDO::FETCH_ASSOC);

    if (!$user) exit;
    $check = $db->prepare("SELECT id FROM reactions WHERE post_id=? AND user_id=?");
    $check->execute([$post_id, $user['id']]);
    $ex = $check->fetch(PDO::FETCH_ASSOC);

    if ($ex) {
        $db->prepare("DELETE FROM reactions WHERE post_id=? AND user_id=?")->execute([$post_id, $user['id']]);
    } else {
        $ins = $db->prepare("INSERT INTO reactions (post_id, user_id, type) VALUES (?,?,?)");
        $ins->execute([$post_id, $user['id'], $type]);
    }
}
