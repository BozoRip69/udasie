<?php
require 'config.php';
require_login($db);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profile) {
    echo "<body style='background:#0046ad;color:white;font-family:Segoe UI;text-align:center;padding:50px'>
            <h2>❌ Taki użytkownik nie istnieje.</h2>
            <a href='users.php' style='color:white;text-decoration:underline;'>Wróć do listy użytkowników</a>
          </body>";
    exit;
}

$avatar = (!empty($profile['avatar']) && file_exists($profile['avatar'])) ? htmlspecialchars($profile['avatar']) : 'default-avatar.png';
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil użytkownika - AutoPart Battery</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    *{margin:0;padding:0;box-sizing:border-box;font-family:"Segoe UI",Arial,sans-serif}
    body{background:linear-gradient(180deg,#0046ad 60%,#003b93 100%);color:white;min-height:100vh;display:flex;flex-direction:column;}
    nav{background:rgba(255,255,255,0.1);backdrop-filter:blur(6px);display:flex;justify-content:space-between;align-items:center;padding:15px 40px;border-bottom:1px solid rgba(255,255,255,0.15);}
    nav img{height:45px;}
    .nav-links{display:flex;align-items:center;gap:20px;}
    .nav-links a{color:#fff;text-decoration:none;transition:0.3s;}
    .nav-links a:hover{color:#dfe9ff;}
    main{flex:1;display:flex;justify-content:center;align-items:flex-start;padding:60px 20px;}
    .profile-box{background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);backdrop-filter:blur(8px);border-radius:16px;padding:40px;width:100%;max-width:700px;text-align:center;box-shadow:0 8px 25px rgba(0,0,0,0.3);}
    .profile-box img{width:140px;height:140px;border-radius:50%;object-fit:cover;border:3px solid rgba(255,255,255,0.7);margin-bottom:15px;}
    .profile-box h1{font-size:1.8rem;margin-bottom:8px;}
    .profile-box p{margin-bottom:5px;opacity:0.9;}
    .bio{background:rgba(255,255,255,0.1);padding:15px;border-radius:12px;margin-top:20px;font-size:0.95rem;text-align:left;white-space:pre-wrap;}
    .stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:15px;margin:25px 0;}
    .stat-box{background:rgba(255,255,255,0.1);padding:15px;border-radius:12px;border:1px solid rgba(255,255,255,0.15);font-size:0.95rem;}
    .stat-box strong{font-size:1.4rem;display:block;margin-top:8px;}
    footer{text-align:center;padding:20px;opacity:0.7;font-size:0.85rem;}
  </style>
</head>
<body>
  <nav>
    <img src="logo.png" alt="AutoPart Battery">
    <div class="nav-links">
      <a href="dashboard.php"><i class="fa-solid fa-gauge"></i> Panel</a>
      <a href="users.php"><i class="fa-solid fa-users"></i> Użytkownicy</a>
      <a href="account.php"><i class="fa-solid fa-gear"></i> Ustawienia</a>
      <form method="post" action="logout.php" style="display:inline;">
        <button type="submit" style="background:white;color:#0046ad;border:none;border-radius:8px;padding:8px 14px;cursor:pointer;">Wyloguj</button>
      </form>
    </div>
  </nav>

  <main>
    <div class="profile-box">
      <img src="<?php echo $avatar; ?>" alt="Awatar">
      <h1><?php echo htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']); ?></h1>
      <p><?php echo htmlspecialchars($profile['email']); ?></p>
      <p><small>Zarejestrowano: <?php echo date('d.m.Y', strtotime($profile['created_at'])); ?></small></p>
      <p><small>Ostatnie logowanie: <?php echo $profile['last_login'] ? date('d.m.Y H:i', strtotime($profile['last_login'])) : 'Brak danych'; ?></small></p>

      <?php if (!empty($profile['bio'])): ?>
        <div class="bio">
          <strong>Opis:</strong><br><?php echo nl2br(htmlspecialchars($profile['bio'])); ?>
        </div>
      <?php endif; ?>

      <div class="stats">
        <div class="stat-box">
          <span>🚗 Samochody</span>
          <strong><?php echo (int)$profile['car_count']; ?></strong>
        </div>
        <div class="stat-box">
          <span>🔋 Akumulatory</span>
          <strong><?php echo (int)$profile['battery_count']; ?></strong>
        </div>
        <div class="stat-box">
          <span>📍 Kilometry</span>
          <strong><?php echo (int)$profile['total_km']; ?> km</strong>
        </div>
      </div>
    </div>
  </main>

  <footer>© 2025 AutoPart Battery — Profil użytkownika</footer>
</body>
</html>
