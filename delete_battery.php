<?php
session_start();
require_once "config.php";

$user = require_login($db);

if (!isset($_POST["id"])) {
    die("Brak ID akumulatora.");
}

$id = $_POST["id"];

// usuwamy tylko jeśli należy do użytkownika
$stmt = $db->prepare("DELETE FROM user_batteries WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $user["id"]]);

header("Location: user_batteries.php?deleted=1");
exit;
?>
