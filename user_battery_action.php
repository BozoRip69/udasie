<?php
require_once "config.php";

$user = require_login($db);
$user_id = $user["id"];

if (!isset($_POST["action"]) || !isset($_POST["id"])) {
    die("Brak danych!");
}

$action = $_POST["action"];
$id = intval($_POST["id"]);

// ---------- DODANIE POMIARU (teraz: wpis do historii + aktualizacja ostatniego pomiaru + alert) ----------
if ($action === "add_measurement") {

    $cca = isset($_POST["cca"]) && $_POST["cca"] !== "" ? intval($_POST["cca"]) : null;
    $voltage = isset($_POST["voltage"]) && $_POST["voltage"] !== "" ? floatval($_POST["voltage"]) : null;

    // Weryfikacja właściciela rekordu user_batteries
    $check = $db->prepare("SELECT id, user_id FROM user_batteries WHERE id = ? AND user_id = ?");
    $check->execute([$id, $user_id]);
    if ($check->rowCount() == 0) {
        die("❌ Brak dostępu!");
    }

    // 1) Wpis do tabeli historycznej
    $ins = $db->prepare("
        INSERT INTO battery_measurements (user_id, user_battery_id, cca, voltage, measured_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $ins->execute([$user_id, $id, $cca, $voltage]);

    // 2) Aktualizacja ostatniego pomiaru w user_batteries (dla szybkiego podglądu)
    $upd = $db->prepare("
        UPDATE user_batteries
        SET cca_measurement = ?, voltage_measurement = ?, measured_at = NOW()
        WHERE id = ? AND user_id = ?
    ");
    $upd->execute([$cca, $voltage, $id, $user_id]);

    // 3) Sprawdź próg CCA i utwórz powiadomienie jeśli poniżej
    // Możesz zmienić próg globalnie — tu przykładowo 600A
    $cca_threshold = 600;

    if ($cca !== null && $cca < $cca_threshold) {
        $msg = "Uwaga — akumulator (ID: $id) ma niski CCA: {$cca}A (próg: {$cca_threshold}A).";
        $note = $db->prepare("INSERT INTO notifications (user_id, type, content, link, is_read) VALUES (?, 'system', ?, ?, 0)");
        // link możesz ustawić do edycji/pomiarów
        $link = "user_batteries.php";
        $note->execute([$user_id, $msg, $link]);
        // Możemy też przekazać parametr w redirect żeby pokazać alert od razu:
        header("Location: user_batteries.php?msg=measurement_added&lowcca=1&battery_id=$id");
        exit;
    }

    header("Location: user_batteries.php?msg=measurement_added");
    exit;
}

// ---------- USUWANIE ----------
if ($action === "delete") {
    $check = $db->prepare("SELECT id FROM user_batteries WHERE id = ? AND user_id = ?");
    $check->execute([$id, $user_id]);

    if ($check->rowCount() == 0) {
        die("❌ Brak dostępu!");
    }

    $del = $db->prepare("DELETE FROM user_batteries WHERE id = ?");
    $del->execute([$id]);

    header("Location: user_batteries.php?msg=deleted");
    exit;
}

echo "Nieznana akcja!";
