<?php
require 'config.php';
$user = require_login($db);

$action = $_POST['action'] ?? null;

if ($action === 'add') {
    $brand = trim($_POST['brand']);
    $model = trim($_POST['model']);
    $year = (int)$_POST['year'];
    $mileage = (int)$_POST['mileage'];

    $imagePath = null;
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = 'uploads/cars/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $fileName = time().'_'.basename($_FILES['image']['name']);
        $target = $uploadDir.$fileName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $imagePath = $target;
        }
    }

    $stmt = $db->prepare("INSERT INTO cars (user_id, brand, model, year, mileage, image, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$user['id'], $brand, $model, $year, $mileage, $imagePath]);

} elseif ($action === 'delete') {
    $id = (int)$_POST['id'];
    $stmt = $db->prepare("DELETE FROM cars WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user['id']]);
}

header("Location: garage.php");
exit;
?>
