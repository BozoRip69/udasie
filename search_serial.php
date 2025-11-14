<?php
require_once "config.php";

if (!isset($_GET["q"])) {
    exit;
}

$q = trim($_GET["q"]);

$stmt = $db->prepare("
    SELECT nr_seryjny, producent, numer_katalogowy, pojemnosc_ah, prad_rozruchowy_en_a
    FROM autopart_akumulatory
    WHERE nr_seryjny LIKE ?
    LIMIT 20
");
$stmt->execute(["%$q%"]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($data as $row):
?>
<div onclick="selectSerial('<?= $row['nr_seryjny'] ?>')">
    <strong><?= $row['nr_seryjny'] ?></strong><br>
    <?= $row['producent'] ?> — <?= $row['numer_katalogowy'] ?>
    (<?= $row['pojemnosc_ah'] ?>Ah / <?= $row['prad_rozruchowy_en_a'] ?>A)
</div>
<?php endforeach; ?>
