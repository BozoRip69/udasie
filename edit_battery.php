<?php

require_once "config.php";

$user = require_login($db);

if (!isset($_GET["id"])) {
    die("Brak ID akumulatora.");
}

$id = $_GET["id"];

// pobieramy dane aku użytkownika
$stmt = $db->prepare("
    SELECT ub.*, 
           a.producent, a.numer_katalogowy, a.nr_seryjny 
    FROM user_batteries ub
    JOIN autopart_akumulatory a ON ub.battery_id = a.id
    WHERE ub.id = ? AND ub.user_id = ?
");
$stmt->execute([$id, $user["id"]]);
$battery = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$battery) {
    die("Akumulator nie istnieje lub nie należy do Ciebie.");
}

// zapis zmian
if (isset($_POST["save"])) {

    $purchase_place = $_POST['purchase_place'];
    $purchase_date = $_POST['purchase_date'];
    $notes = $_POST['notes'];

    $sql = "UPDATE user_batteries SET 
            purchase_place = ?, purchase_date = ?, notes = ?";
    $params = [$purchase_place, $purchase_date, $notes];

    if (!empty($_FILES['battery_image']['name'])) {
        $new = "uploads/batteries/" . time() . "_" . basename($_FILES['battery_image']['name']);
        move_uploaded_file($_FILES['battery_image']['tmp_name'], $new);
        $sql .= ", battery_image = ?";
        $params[] = $new;
    }

    if (!empty($_FILES['receipt_image']['name'])) {
        $new = "uploads/receipts/" . time() . "_" . basename($_FILES['receipt_image']['name']);
        move_uploaded_file($_FILES['receipt_image']['tmp_name'], $new);
        $sql .= ", receipt_image = ?";
        $params[] = $new;
    }

    $sql .= " WHERE id = ? AND user_id = ?";
    $params[] = $id;
    $params[] = $user["id"];

    $stmtUpdate = $db->prepare($sql);
    $stmtUpdate->execute($params);

    header("Location: user_batteries.php?updated=1");
    exit;
}
?>

<?php include "includes/header.php"; ?>

<main class="card">
    <h1>Edytuj akumulator</h1>

    <p><strong>Producent:</strong> <?= $battery['producent'] ?></p>
    <p><strong>Model:</strong> <?= $battery['numer_katalogowy'] ?></p>
    <p><strong>Numer seryjny:</strong> <?= $battery['nr_seryjny'] ?></p>

    <form method="POST" enctype="multipart/form-data">

        <label>Miejsce zakupu:</label>
        <input type="text" name="purchase_place" value="<?= $battery['purchase_place'] ?>">

        <label>Data zakupu:</label>
        <input type="date" name="purchase_date" value="<?= $battery['purchase_date'] ?>">

        <label>Nowe zdjęcie akumulatora:</label>
        <input type="file" name="battery_image">

        <label>Nowy paragon:</label>
        <input type="file" name="receipt_image">

        <label>Uwagi:</label>
        <textarea name="notes"><?= $battery['notes'] ?></textarea>

        <button name="save" class="btn">Zapisz zmiany</button>
    </form>

    <br>

    <a class="btn" href="user_batteries.php">⬅ Powrót</a>
</main>

<?php include "includes/footer.php"; ?>
