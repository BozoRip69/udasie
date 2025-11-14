<?php
require_once "config.php";

$user = require_login($db);
$user_id = $user["id"];

if (!isset($_GET["id"])) {
    die("Brak ID akumulatora!");
}

$id = intval($_GET["id"]);

/**************************************
 * Pobranie danych rekordu
 **************************************/
$stmt = $db->prepare("
    SELECT ub.*, a.producent, a.numer_katalogowy, a.nr_seryjny
    FROM user_batteries ub
    JOIN autopart_akumulatory a ON ub.battery_id = a.id
    WHERE ub.id = ? AND ub.user_id = ?
");
$stmt->execute([$id, $user_id]);

$battery = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$battery) {
    die("❌ Brak dostępu!");
}

/**************************************
 * Pobranie aut użytkownika
 **************************************/
$cars_stmt = $db->prepare("SELECT * FROM vehicles WHERE user_id = ?");
$cars_stmt->execute([$user_id]);
$user_cars = $cars_stmt->fetchAll(PDO::FETCH_ASSOC);

/**************************************
 * Zapis zmian
 **************************************/
if (isset($_POST["save"])) {

    $vehicle_id = !empty($_POST["vehicle_id"]) ? $_POST["vehicle_id"] : null;
    $purchase_place = trim($_POST["purchase_place"]);
    $purchase_date = $_POST["purchase_date"];
    $notes = trim($_POST["notes"]);

    $battery_img = $battery["battery_image"];
    $receipt_img = $battery["receipt_image"];

    if (!empty($_FILES["battery_image"]["name"])) {
        $battery_img = "uploads/batteries/" . time() . "_" . basename($_FILES["battery_image"]["name"]);
        move_uploaded_file($_FILES["battery_image"]["tmp_name"], $battery_img);
    }

    if (!empty($_FILES["receipt_image"]["name"])) {
        $receipt_img = "uploads/receipts/" . time() . "_" . basename($_FILES["receipt_image"]["name"]);
        move_uploaded_file($_FILES["receipt_image"]["tmp_name"], $receipt_img);
    }

    $upd = $db->prepare("
        UPDATE user_batteries
        SET vehicle_id = ?, purchase_place = ?, purchase_date = ?, 
            battery_image = ?, receipt_image = ?, notes = ?
        WHERE id = ? AND user_id = ?
    ");

    $upd->execute([
        $vehicle_id,
        $purchase_place,
        $purchase_date,
        $battery_img,
        $receipt_img,
        $notes,
        $id,
        $user_id
    ]);

    header("Location: user_batteries.php?msg=updated");
    exit;
}

include "includes/header.php";
?>

<main>
<h1>✏️ Edycja akumulatora</h1>

<div class="card">

    <h2><?= $battery["producent"] ?> — <?= $battery["numer_katalogowy"] ?></h2>
    <p><strong>Numer seryjny:</strong> <?= $battery["nr_seryjny"] ?></p>

    <form method="POST" enctype="multipart/form-data">

        <label>Samochód:</label>
        <select name="vehicle_id">
            <option value="">— bez przypisania —</option>

            <?php foreach ($user_cars as $car): ?>
                <option value="<?= $car['id'] ?>" 
                    <?= $battery["vehicle_id"] == $car['id'] ? "selected" : "" ?>>
                    <?= $car['brand'] ?> <?= $car['model'] ?> (<?= $car['year'] ?>)
                </option>
            <?php endforeach; ?>
        </select>

        <label>Miejsce zakupu:</label>
        <input type="text" name="purchase_place" value="<?= $battery['purchase_place'] ?>">

        <label>Data zakupu:</label>
        <input type="date" name="purchase_date" value="<?= $battery['purchase_date'] ?>">

        <label>Zdjęcie akumulatora:</label>
        <?php if ($battery["battery_image"]): ?>
            <p><img src="<?= $battery["battery_image"] ?>" style="max-width:200px;"></p>
        <?php endif; ?>
        <input type="file" name="battery_image">

        <label>Paragon:</label>
        <?php if ($battery["receipt_image"]): ?>
            <p><img src="<?= $battery["receipt_image"] ?>" style="max-width:200px;"></p>
        <?php endif; ?>
        <input type="file" name="receipt_image">

        <label>Uwagi:</label>
        <textarea name="notes"><?= $battery["notes"] ?></textarea>

        <button type="submit" name="save">Zapisz zmiany</button>

    </form>
</div>

</main>

<?php include "includes/footer.php"; ?>
