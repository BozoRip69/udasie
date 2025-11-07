<?php
require 'config.php';
$user = require_login($db);
$pageTitle = "Panel główny";
include 'includes/header.php';
?>

<section class="dashboard">
  <h1>Witaj, <?= htmlspecialchars($user['first_name']) ?> 👋</h1>
  <div class="dashboard-cards">
    <div class="card">
      <i class="fa-solid fa-car"></i>
      <h2>Twoje samochody</h2>
      <p>Zarządzaj swoim garażem</p>
      <a href="garage.php" class="btn-small">Przejdź</a>
    </div>
    <div class="card">
      <i class="fa-solid fa-battery-half"></i>
      <h2>Akumulatory</h2>
      <p>Przeglądaj i aktualizuj</p>
      <a href="my_batteries.php" class="btn-small">Przejdź</a>
    </div>
    <div class="card">
      <i class="fa-solid fa-newspaper"></i>
      <h2>Posty społeczności</h2>
      <p>Sprawdź, co nowego!</p>
      <a href="browse.php" class="btn-small">Przejdź</a>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
