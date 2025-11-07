<?php
require 'config.php';
$user = require_login($db);

$receiver_id = (int)($_POST['receiver_id'] ?? 0);
$content = trim($_POST['message'] ?? '');
$image_path = null;

// 🖼️ Obsługa uploadu zdjęcia
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/messages/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $fileName = time() . '_' . basename($_FILES['image']['name']);
    $targetPath = $uploadDir . $fileName;

    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (in_array($ext, $allowed)) {
        move_uploaded_file($_FILES['image']['tmp_name'], $targetPath);
        $image_path = $targetPath;
    }
}

// 📩 Zapis wiadomości
if ($receiver_id && ($content !== '' || $image_path)) {
    $stmt = $db->prepare("
        INSERT INTO messages (sender_id, receiver_id, content, image_path, created_at, is_read)
        VALUES (?, ?, ?, ?, NOW(), 0)
    ");
    $stmt->execute([$user['id'], $receiver_id, $content, $image_path]);
}

echo 'OK';
?>
