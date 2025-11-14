<?php
require_once "config.php";

$user = require_login($db);
$user_id = $user["id"];

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode([]);
    exit;
}

$ub_id = intval($_GET['id']);

// sprawdzenie właściciela
$check = $db->prepare("SELECT id FROM user_batteries WHERE id = ? AND user_id = ?");
$check->execute([$ub_id, $user_id]);
if ($check->rowCount() == 0) {
    http_response_code(403);
    echo json_encode([]);
    exit;
}

$stmt = $db->prepare("
    SELECT cca, voltage, measured_at
    FROM battery_measurements
    WHERE user_battery_id = ?
    ORDER BY measured_at ASC
    LIMIT 1000
");
$stmt->execute([$ub_id]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($data);
exit;
