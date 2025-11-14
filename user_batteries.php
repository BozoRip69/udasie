<?php
require_once "config.php";

$user = require_login($db);
$user_id = $user["id"];

$message = "";

/***********************************************
 * DODAWANIE AKUMULATORA
 ***********************************************/
if (isset($_POST["add_battery"])) {

    $serial = trim($_POST["serial_number"]);
    $purchase_place = trim($_POST["purchase_place"]);
    $purchase_date = $_POST["purchase_date"];
    $notes = trim($_POST["notes"]);
    $vehicle_id = !empty($_POST["vehicle_id"]) ? $_POST["vehicle_id"] : null;

    // Uploady zdjęć
    $battery_img = null;
    $receipt_img = null;

    if (!empty($_FILES["battery_image"]["name"])) {
        $battery_img = "uploads/batteries/" . time() . "_" . basename($_FILES["battery_image"]["name"]);
        move_uploaded_file($_FILES["battery_image"]["tmp_name"], $battery_img);
    }

    if (!empty($_FILES["receipt_image"]["name"])) {
        $receipt_img = "uploads/receipts/" . time() . "_" . basename($_FILES["receipt_image"]["name"]);
        move_uploaded_file($_FILES["receipt_image"]["tmp_name"], $receipt_img);
    }

    // Szukamy akumulatora w bazie głównej
    $stmt = $db->prepare("SELECT id FROM autopart_akumulatory WHERE nr_seryjny = ?");
    $stmt->execute([$serial]);

    if ($stmt->rowCount() == 0) {
        $message = "❌ Nie znaleziono akumulatora o podanym numerze seryjnym!";
    } else {

        $battery_id = $stmt->fetch(PDO::FETCH_ASSOC)["id"];

        $insert = $db->prepare("
            INSERT INTO user_batteries
            (user_id, vehicle_id, battery_id, purchase_place, purchase_date, battery_image, receipt_image, notes)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $insert->execute([
            $user_id,
            $vehicle_id,
            $battery_id,
            $purchase_place,
            $purchase_date,
            $battery_img,
            $receipt_img,
            $notes
        ]);

        $message = "✅ Akumulator został dodany!";
    }
}

/***********************************************
 * LISTA AKUMULATORÓW UŻYTKOWNIKA
 ***********************************************/
$stmt = $db->prepare("
    SELECT ub.*,
           a.producent, a.numer_katalogowy, a.pojemnosc_ah, a.napiecie_v,
           a.prad_rozruchowy_en_a, a.dlugosc_mm, a.szerokosc_mm, a.wysokosc_mm,
           a.waga_kg, a.uklad_polaczen, a.koncowki, a.mocowanie, a.opis,
           a.zdjecie_url, a.nr_seryjny,
           v.brand, v.model, v.year, v.registration_number
    FROM user_batteries ub
    JOIN autopart_akumulatory a ON ub.battery_id = a.id
    LEFT JOIN vehicles v ON ub.vehicle_id = v.id
    WHERE ub.user_id = ?
    ORDER BY ub.added_at DESC
");
$stmt->execute([$user_id]);
$batteries = $stmt->fetchAll(PDO::FETCH_ASSOC);

/***********************************************
 * LISTA AUT UŻYTKOWNIKA
 ***********************************************/
$cars_stmt = $db->prepare("SELECT * FROM vehicles WHERE user_id = ?");
$cars_stmt->execute([$user_id]);
$user_cars = $cars_stmt->fetchAll(PDO::FETCH_ASSOC);

include "includes/header.php";
?>

<style>
.show { display: block !important; }
.tech-table-container {
    margin-top: 15px;
    border-radius: 12px;
    overflow: hidden;
}
.tech-table-header {
    background: #003b93;
    color: white;
    padding: 12px;
    font-size: 18px;
    font-weight: bold;
}
.tech-table {
    width: 100%;
    border-collapse: collapse;
}
.tech-table tr { border-bottom: 1px solid #eee; }
.tech-table td {
    padding: 10px 15px;
}
.tech-table td:first-child {
    width: 40%;
    opacity: .7;
    text-align: right;
}
#serial_list {
    position: absolute;
    background: white;
    border: 1px solid #ccc;
    display: none;
    max-height: 250px;
    overflow-y: auto;
    z-index: 999;
}
#serial_list div {
    padding: 10px;
    cursor: pointer;
}
#serial_list div:hover { background: #eee; }
</style>

<main>

<h1>🔋 Twoje akumulatory</h1>

<?php if (!empty($message)): ?>
    <div class="card" style="border-left: 4px solid #0078ff;"><?= $message ?></div>
<?php endif; ?>

<div class="card">
    <h2>➕ Dodaj akumulator</h2>

    <form method="POST" enctype="multipart/form-data">

        <label>Numer seryjny:</label>
        <div style="position: relative;">
            <input type="text" name="serial_number" id="serial_number" required>
            <button type="button" onclick="searchSerial()" style="position:absolute; right:10px; top:7px; background:none; border:none; font-size:18px;">🔍</button>
            <div id="serial_list"></div>
        </div>

        <label>Samochód:</label>
        <select name="vehicle_id">
            <option value="">— bez przypisania —</option>
            <?php foreach ($user_cars as $car): ?>
                <option value="<?= $car['id'] ?>">
                    <?= $car['brand'] ?> <?= $car['model'] ?> (<?= $car['year'] ?>)
                    <?= $car['registration_number'] ? "— ".$car['registration_number'] : "" ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Miejsce zakupu:</label>
        <input type="text" name="purchase_place">

        <label>Data zakupu:</label>
        <input type="date" name="purchase_date">

        <label>Zdjęcie akumulatora:</label>
        <input type="file" name="battery_image">

        <label>Paragon:</label>
        <input type="file" name="receipt_image">

        <label>Uwagi:</label>
        <textarea name="notes"></textarea>

        <button type="submit" name="add_battery">Dodaj akumulator</button>
    </form>
</div>

<h2>📄 Lista akumulatorów</h2>

<?php foreach ($batteries as $row): ?>

<div class="card">

    <h3><?= $row["producent"] ?> — <?= $row["numer_katalogowy"] ?></h3>

    <p><strong>Numer seryjny:</strong> <?= $row["nr_seryjny"] ?></p>

    <p><strong>Przypisany samochód:</strong>
        <?php if ($row["vehicle_id"]): ?>
            🚗 <?= $row['brand'] ?> <?= $row['model'] ?> (<?= $row['year'] ?>)
        <?php else: ?>
            brak
        <?php endif; ?>
    </p>

    <?php if ($row["battery_image"]): ?>
        <p><strong>Zdjęcie:</strong><br><img src="<?= $row["battery_image"] ?>" style="max-width:200px;"></p>
    <?php endif; ?>

    <?php if ($row["receipt_image"]): ?>
        <p><strong>Paragon:</strong><br><img src="<?= $row["receipt_image"] ?>" style="max-width:200px;"></p>
    <?php endif; ?>

    <p><strong>Pojemność:</strong> <?= $row["pojemnosc_ah"] ?> Ah</p>
    <p><strong>Napięcie:</strong> <?= $row["napiecie_v"] ?> V</p>
    <p><strong>Prąd rozruchowy:</strong> <?= $row["prad_rozruchowy_en_a"] ?> A</p>

    <?php if ($row["zdjecie_url"]): ?>
        <a href="<?= $row["zdjecie_url"] ?>" target="_blank">🔗 Strona produktu</a><br>
    <?php endif; ?>

    <button onclick="document.getElementById('det-<?= $row['id'] ?>').classList.toggle('show')">Dane techniczne</button>

    <div id="det-<?= $row['id'] ?>" class="tech-table-container" style="display:none;">
        <div class="tech-table-header">Dane techniczne</div>
        <table class="tech-table">
            <tr><td>Numer katalogowy</td><td><?= $row["numer_katalogowy"] ?></td></tr>
            <tr><td>Pojemność</td><td><?= $row["pojemnosc_ah"] ?> Ah</td></tr>
            <tr><td>Napięcie</td><td><?= $row["napiecie_v"] ?> V</td></tr>
            <tr><td>Prąd rozruchowy</td><td><?= $row["prad_rozruchowy_en_a"] ?> A</td></tr>
            <tr><td>Wymiary</td><td><?= $row["dlugosc_mm"] ?> × <?= $row["szerokosc_mm"] ?> × <?= $row["wysokosc_mm"] ?> mm</td></tr>
            <tr><td>Układ połączeń</td><td><?= $row["uklad_polaczen"] ?></td></tr>
            <tr><td>Końcówki</td><td><?= $row["koncowki"] ?></td></tr>
            <tr><td>Mocowanie</td><td><?= $row["mocowanie"] ?></td></tr>
            <tr><td>Waga</td><td><?= $row["waga_kg"] ?> kg</td></tr>
            <tr><td>Opis</td><td><?= $row["opis"] ?></td></tr>
        </table>
    </div>

    <hr>

    <a class="btn" href="user_battery_edit.php?id=<?= $row["id"] ?>">Edytuj</a>

    <form method="POST" action="user_battery_action.php" onsubmit="return confirm('Usunąć akumulator?');">
        <input type="hidden" name="id" value="<?= $row['id'] ?>">
        <input type="hidden" name="action" value="delete">
        <button class="danger">Usuń</button>
    </form>

    <hr>

<hr>

    <hr>

    <!-- FORMULARZ POMIARÓW -->
    <form method="POST" action="user_battery_action.php" style="margin-top:10px;">
        <input type="hidden" name="action" value="add_measurement">
        <input type="hidden" name="id" value="<?= $row['id'] ?>">

        <h4>➕ Dodaj pomiary</h4>

        <label>CCA (A):</label>
        <input type="number" name="cca" placeholder="np. 780">

        <label>Napięcie (V):</label>
        <input type="number" step="0.01" name="voltage" placeholder="np. 12.65">

        <button type="submit">Zapisz pomiary</button>
    </form>

    <?php if ($row['cca_measurement'] || $row['voltage_measurement']): ?>
        <p><strong>Ostatni pomiar:</strong><br>
            CCA: <?= $row['cca_measurement'] ?: "brak" ?> A<br>
            Napięcie: <?= $row['voltage_measurement'] ?: "brak" ?> V<br>
            Data: <?= $row['measured_at'] ?: "—" ?>
        </p>
    <?php endif; ?>

    <!-- PRZYCISK HISTORII + KANWY DLA WYKRESÓW -->
    <button type="button" onclick="toggleHistory(<?= $row['id'] ?>)">Pokaż historię pomiarów</button>

    <div id="history-<?= $row['id'] ?>" style="display:none; margin-top:10px;">
        <canvas id="cca-chart-<?= $row['id'] ?>" height="150"></canvas>
        <canvas id="voltage-chart-<?= $row['id'] ?>" height="120" style="margin-top:12px;"></canvas>
        <div id="history-loader-<?= $row['id'] ?>" style="display:none;">Ładowanie...</div>
    </div>

    <hr>




</div>

<?php endforeach; ?>

</main>

<script>
let input = document.getElementById("serial_number");
let list = document.getElementById("serial_list");

input.addEventListener("keyup", () => list.style.display = "none");

function searchSerial() {
    let value = input.value.trim();

    if (value.length < 2) {
        alert("Wpisz minimum 2 znaki!");
        return;
    }

    fetch("search_serial.php?q=" + value)
        .then(res => res.text())
        .then(html => {
            if (html.trim() === "") {
                alert("Brak wyników!");
                list.style.display = "none";
            } else {
                list.innerHTML = html;
                list.style.display = "block";
            }
        });
}

function selectSerial(v) {
    input.value = v;
    list.style.display = "none";
}
</script>
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const openCharts = {}; // cache wykresów, żeby nie rysować ponownie

function toggleHistory(id) {
    const box = document.getElementById('history-' + id);
    if (!box) return;
    if (box.style.display === 'none' || box.style.display === '') {
        box.style.display = 'block';
        // jeśli jeszcze nie załadowano danych, pobierz i narysuj
        if (!openCharts[id]) {
            loadAndDraw(id);
        }
    } else {
        box.style.display = 'none';
    }
}

function loadAndDraw(id) {
    const loader = document.getElementById('history-loader-' + id);
    loader && (loader.style.display = 'block');

    fetch('measurement_data.php?id=' + id)
        .then(res => {
            if (!res.ok) throw new Error('Błąd pobierania danych');
            return res.json();
        })
        .then(data => {
            loader && (loader.style.display = 'none');
            if (!Array.isArray(data) || data.length === 0) {
                const box = document.getElementById('history-' + id);
                box.innerHTML = "<p>Brak zapisanych pomiarów.</p>";
                return;
            }

            // przygotuj dane
            const labels = data.map(r => r.measured_at);
            const ccaData = data.map(r => r.cca === null ? null : Number(r.cca));
            const voltageData = data.map(r => r.voltage === null ? null : Number(r.voltage));

            // CCA chart
            const ccaCtx = document.getElementById('cca-chart-' + id).getContext('2d');
            const ccaChart = new Chart(ccaCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'CCA (A)',
                        data: ccaData,
                        spanGaps: true,
                        tension: 0.2,
                        fill: false,
                    }]
                },
                options: {
                    scales: {
                        x: { display: true, title: { display: false } },
                        y: { display: true, title: { display: true, text: 'A' } }
                    },
                    plugins: { legend: { display: true } }
                }
            });

            // Voltage chart
            const voltCtx = document.getElementById('voltage-chart-' + id).getContext('2d');
            const voltChart = new Chart(voltCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Napięcie (V)',
                        data: voltageData,
                        spanGaps: true,
                        tension: 0.2,
                        fill: false,
                    }]
                },
                options: {
                    scales: {
                        x: { display: true },
                        y: { display: true, title: { display: true, text: 'V' } }
                    },
                    plugins: { legend: { display: true } }
                }
            });

            openCharts[id] = { ccaChart, voltChart };
        })
        .catch(err => {
            loader && (loader.style.display = 'none');
            console.error(err);
            alert('Błąd podczas pobierania danych pomiarów.');
        });
}
</script>


<?php include "includes/footer.php"; ?>
