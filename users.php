<?php
require 'config.php';
require_login($db);

// pobierz wszystkich użytkowników
$stmt = $db->query("SELECT id, first_name, last_name, email, phone, avatar, created_at, last_login, car_count, battery_count, total_km FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Użytkownicy - AutoPart Battery</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    *{box-sizing:border-box;font-family:"Segoe UI",Arial,sans-serif;margin:0;padding:0}
    body{background:linear-gradient(180deg,#0046ad 60%,#003b93 100%);color:#fff;min-height:100vh;display:flex;flex-direction:column}
    nav{background:rgba(255,255,255,0.1);backdrop-filter:blur(6px);display:flex;justify-content:space-between;align-items:center;padding:12px 30px;border-bottom:1px solid rgba(255,255,255,0.12)}
    nav img{height:42px}
    .nav-links{display:flex;gap:14px;align-items:center}
    .nav-links a{color:#fff;text-decoration:none}
    main{flex:1;padding:30px;display:flex;justify-content:center}
    .card{width:100%;max-width:1100px;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.12);border-radius:12px;padding:20px}
    table{width:100%;border-collapse:collapse;color:#fff}
    th,td{padding:10px;text-align:left;border-bottom:1px solid rgba(255,255,255,0.06);vertical-align:middle}
    th{opacity:0.9;font-weight:600}
    .avatar{width:48px;height:48px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,0.2)}
    .small{font-size:0.85rem;opacity:0.9}
    @media(max-width:800px){table{font-size:0.85rem}td:nth-child(4){display:none}}
  </style>
</head>
<body>
  <nav>
    <img src="logo.png" alt="logo">
    <div class="nav-links">
      <a href="browse.php"><i class="fa-solid fa-newspaper"></i> Przeglądanie</a>
      <a href="dashboard.php"><i class="fa-solid fa-gauge"></i> Panel</a>
      <a href="account.php"><i class="fa-solid fa-gear"></i> Ustawienia</a>
      <a href="users.php"><i class="fa-solid fa-users"></i> Użytkownicy</a>
      <form method="post" action="logout.php" style="display:inline"><button style="background:#fff;color:#0046ad;border:0;padding:8px 12px;border-radius:8px;cursor:pointer">Wyloguj</button></form>
    </div>
  </nav>

  <main>
    <div class="card">
      <h2 style="margin-bottom:12px">Lista użytkowników</h2>
      <div style="overflow:auto">
      <table>
        <thead>
          <tr>
            <th>Użytkownik</th>
            <th>Email</th>
            <th>Telefon</th>
            <th class="small">Zarejestrowano</th>
            <th class="small">Ostatnie logowanie</th>
            <th class="small">Samochody</th>
            <th class="small">Akumulatory</th>
            <th class="small">Kilometry</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $u): ?>
            <tr>
              <td>
                <div style="display:flex;align-items:center;gap:10px">
                  <img src="<?php echo $u['avatar'] ? htmlspecialchars($u['avatar']) : 'default-avatar.png'; ?>" alt="a" class="avatar">
                  <div>
                    <a href="profile.php?id=<?php echo $u['id']; ?>" style="color:white;text-decoration:none;font-weight:600;">
  <?php echo htmlspecialchars(trim($u['first_name'].' '.$u['last_name'])); ?>
</a>
<br>
                    <span class="small"><?php echo htmlspecialchars($u['email']); ?></span>
                  </div>
                </div>
              </td>
              <td><?php echo htmlspecialchars($u['email']); ?></td>
              <td><?php echo htmlspecialchars($u['phone']); ?></td>
              <td class="small"><?php echo $u['created_at'] ? date('d.m.Y', strtotime($u['created_at'])) : '-'; ?></td>
              <td class="small"><?php echo $u['last_login'] ? date('d.m.Y H:i', strtotime($u['last_login'])) : '-'; ?></td>
              <td><?php echo (int)$u['car_count']; ?></td>
              <td><?php echo (int)$u['battery_count']; ?></td>
              <td><?php echo (int)$u['total_km']; ?> km</td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      </div>
    </div>
  </main>
</body>
</html>
