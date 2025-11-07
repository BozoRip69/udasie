<?php
require 'config.php';
$user = require_login($db);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: garage.php");
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'update' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];

    // Pobierz istniejący wpis, by sprawdzić właściciela i aktualne zdjęcie
    $s = $db->prepare("SELECT * FROM vehicles WHERE id = ? AND user_id = ?");
    $s->execute([$id, $user['id']]);
    $vehicle = $s->fetch(PDO::FETCH_ASSOC);
    if (!$vehicle) {
        header("Location: garage.php");
        exit;
    }

    $imagePath = $vehicle['image'];

    // Obsługa uploadu pliku
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/vehicles/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
        $filename = time() . "_" . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', basename($_FILES["image"]["name"]));
        $targetFile = $targetDir . $filename;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $imagePath = $targetFile;
        }
    }

    // Pobieramy wartości z formularza (zabezpieczone przez prepared statements)
    $registration_number = $_POST['registration_number'] ?: null;
    $mileage = is_numeric($_POST['mileage']) ? (int)$_POST['mileage'] : 0;
    $purchase_date = !empty($_POST['purchase_date']) ? $_POST['purchase_date'] : null;
    $inspection_date = !empty($_POST['inspection_date']) ? $_POST['inspection_date'] : null;

    $stmt = $db->prepare("UPDATE vehicles SET registration_number = ?, mileage = ?, purchase_date = ?, inspection_date = ?, image = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([
        $registration_number,
        $mileage,
        $purchase_date,
        $inspection_date,
        $imagePath,
        $id,
        $user['id']
    ]);

    header("Location: garage.php?updated=1");
    exit;
}

if ($action === 'delete' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    // opcjonalnie: usuń plik image z dysku jeśli chcesz
    $stmt = $db->prepare("DELETE FROM vehicles WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user['id']]);
    header("Location: garage.php?deleted=1");
    exit;
}

// domyślnie przekieruj
header("Location: garage.php");
exit;
