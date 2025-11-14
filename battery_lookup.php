<?php
require 'config.php';

$serial = $_GET['serial'] ?? '';

$stmt = $db->prepare("SELECT * FROM autopart_akumulatory WHERE nr_seryjny LIKE ? LIMIT 10");
$stmt->execute(["%$serial%"]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
