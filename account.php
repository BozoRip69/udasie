<?php
require 'config.php';
require_login($db);

$userEmail = $_SESSION['user_email'];
$message = "";

// Pobierz dane użytkownika
$stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$userEmail]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: login.html");
    exit;
}

// --- AKTUALIZACJA DANYCH OSOBOWYCH ---
if (isset($_POST['update_profile'])) {
    $first = trim($_POST['first_name']);
    $last = trim($_POST['last_name']);
    $phone = trim($_POST['phone']);
    $avatarPath = $user['avatar'];

    // Upload awatara
    if (!empty($_FILES['avatar']['name'])) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileName = time() . '_' . basename($_FILES['avatar']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath)) {
            // usuń stary awatar, jeśli istnieje
            if ($user['avatar'] && file_exists($user['avatar'])) {
                unlink($user['avatar']);
            }
            $avatarPath = $targetPath;
        }
    }

    $bio = trim($_POST['bio'] ?? '');

    $countryCode = $_POST['country_code'] ?? '+48';
    $address = trim($_POST['address'] ?? '');
    $bio = trim($_POST['bio'] ?? '');

    $update = $db->prepare("UPDATE users SET first_name=?, last_name=?, country_code=?, phone=?, address=?, avatar=?, bio=? WHERE email=?");
    $update->execute([$first, $last, $countryCode, $phone, $address, $avatarPath, $bio, $userEmail]);


    $message = "<div class='success'>✅ Dane zostały zaktualizowane.</div>";

    // odśwież dane
    $stmt->execute([$userEmail]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

// --- ZMIANA HASŁA ---
if (isset($_POST['change_password'])) {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if ($new !== $confirm) {
        $message = "<div class='error'>❌ Nowe hasła nie są takie same.</div>";
    } elseif (!password_verify($current, $user['password'])) {
        $message = "<div class='error'>❌ Błędne aktualne hasło.</div>";
    } else {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $db->prepare("UPDATE users SET password=?, session_token=NULL WHERE id=?")->execute([$hash, $user['id']]);
        session_unset();
        session_destroy();
        header("Location: login.html?msg=pass_changed");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Ustawienia konta - AutoPart Battery</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    * {margin:0;padding:0;box-sizing:border-box;font-family:"Segoe UI",Arial,sans-serif;}
    body {
      background:linear-gradient(180deg,#0046ad 60%,#003b93 100%);
      color:white;min-height:100vh;display:flex;flex-direction:column;
    }
    nav {
      background:rgba(255,255,255,0.1);
      backdrop-filter:blur(6px);
      display:flex;justify-content:space-between;align-items:center;
      padding:15px 40px;border-bottom:1px solid rgba(255,255,255,0.15);
      box-shadow:0 4px 10px rgba(0,0,0,0.2);
    }
    nav img{height:45px;}
    .nav-links{display:flex;align-items:center;gap:20px;}
    .nav-links a{color:#fff;text-decoration:none;font-weight:500;transition:0.2s;}
    .nav-links a:hover{color:#dfe9ff;}
    .logout-btn{background:white;color:#0046ad;border:none;border-radius:8px;padding:8px 14px;font-size:0.9rem;cursor:pointer;}
    main{flex:1;display:flex;justify-content:center;align-items:flex-start;padding:60px 20px;}
    .settings-container{
      background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);
      backdrop-filter:blur(8px);border-radius:16px;padding:36px 44px;
      width:100%;max-width:800px;text-align:center;box-shadow:0 8px 25px rgba(0,0,0,0.3);
      animation:fadeIn 0.8s ease forwards;
    }
    .settings-container h1{font-size:1.8rem;margin-bottom:20px;}
    .profile-avatar img{
      width:120px;height:120px;border-radius:50%;object-fit:cover;
      border:3px solid rgba(255,255,255,0.7);margin-bottom:15px;
    }
    .stats{
      display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));
      gap:15px;margin:25px 0;
    }
    .stat-box{
      background:rgba(255,255,255,0.1);
      padding:15px;border-radius:12px;
      border:1px solid rgba(255,255,255,0.15);
      font-size:0.95rem;
    }
    .stat-box strong{font-size:1.4rem;display:block;margin-top:8px;}
    form{text-align:left;margin-top:15px;}
    label{display:block;font-size:0.9rem;margin-bottom:6px;color:#dfe9ff;}
    input{
      width:100%;padding:12px;border:none;border-radius:8px;
      background:rgba(255,255,255,0.9);font-size:1rem;color:#003b93;margin-bottom:14px;
    }
    input:focus{outline:none;box-shadow:0 0 0 3px rgba(255,255,255,0.3);}
    .save-btn{
      width:100%;background:white;color:#0046ad;border:none;border-radius:8px;
      padding:12px;font-size:1rem;cursor:pointer;margin-top:10px;
    }
    .save-btn:hover{background:#dfe9ff;}
    .error,.success{
      margin-top:14px;padding:12px;border-radius:8px;font-size:0.95rem;animation:fadeIn 0.4s ease;
    }
    .error{background:rgba(255,0,0,0.12);color:#ffdede;}
    .success{background:rgba(0,255,0,0.12);color:#baffba;}
    footer{text-align:center;padding:20px;font-size:0.85rem;opacity:0.75;}
    @keyframes fadeIn{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:none;}}
    @media(max-width:600px){nav{padding:15px 20px;flex-direction:column;gap:10px;}.settings-container{padding:22px;}}
    /* Grupa telefonu */
.phone-group {
  display: flex;
  flex-direction: column;
  margin-bottom: 18px;
}

/* kontener dla select + input */
.phone-input-wrapper {
  display: flex;
  align-items: center;
  background: rgba(255, 255, 255, 0.9);
  border-radius: 8px;
  overflow: hidden;
  border: 1px solid rgba(255, 255, 255, 0.2);
}

/* select z flagą */
.phone-input-wrapper select {
  appearance: none;
  border: none;
  background: transparent;
  padding: 5px 10px;
  font-size: 1rem;
  color: #003b93;
  width: 100px;
  cursor: pointer;
  text-align: center;
  font-weight: 500;
}

/* telefon po prawej */
.phone-input-wrapper input[type="text"] {
  flex: 1;
  border: none;
  background: transparent;
  padding: 12px 14px;
  font-size: 1rem;
  color: #003b93;
  outline: none;
}

/* hover i focus */
.phone-input-wrapper select:focus,
.phone-input-wrapper input:focus {
  outline: none;
  background: rgba(255, 255, 255, 1);
}

/* dla mobile */
@media (max-width: 480px) {
  .phone-input-wrapper {
    flex-direction: row;
  }
  .phone-input-wrapper select {
    width: 85px;
    font-size: 0.95rem;
  }
}

  </style>
</head>
<body>
  <nav>
    <img src="logo.png" alt="AutoPart Battery">
    <div class="nav-links">
      <a href="browse.php"><i class="fa-solid fa-newspaper"></i> Przeglądanie</a>
      <a href="users.php"><i class="fa-solid fa-users"></i> Użytkownicy</a>  
      <a href="dashboard.php"><i class="fa-solid fa-gauge"></i> Panel główny</a>
      <a href="account.php"><i class="fa-solid fa-gear"></i> Ustawienia konta</a>
      <form method="post" action="logout.php" style="display:inline;">
        <button type="submit" class="logout-btn">Wyloguj</button>
      </form>
    </div>
  </nav>

  <main>
    <div class="settings-container">
      <div class="profile-avatar">
        <img src="<?php echo $user['avatar'] ? htmlspecialchars($user['avatar']) : 'default-avatar.png'; ?>" alt="Awatar">
      </div>

      <h1><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h1>
      <p><?php echo htmlspecialchars($user['email']); ?></p>
      <p><small>Zarejestrowano: <?php echo date('d.m.Y', strtotime($user['created_at'])); ?></small></p>
      <p><small>Ostatnie logowanie: <?php echo $user['last_login'] ? date('d.m.Y H:i', strtotime($user['last_login'])) : 'Brak danych'; ?></small></p>

      <div class="stats">
        <div class="stat-box">
          <span>🚗 Samochody</span>
          <strong><?php echo (int)$user['car_count']; ?></strong>
        </div>
        <div class="stat-box">
          <span>🔋 Akumulatory</span>
          <strong><?php echo (int)$user['battery_count']; ?></strong>
        </div>
        <div class="stat-box">
          <span>📍 Kilometry</span>
          <strong><?php echo (int)$user['total_km']; ?> km</strong>
        </div>
      </div>

      <hr style="border: none; border-top: 1px solid rgba(255,255,255,0.2); margin: 25px 0;">
<h2 style="margin-bottom:10px;">Edycja danych</h2>
<form method="POST" enctype="multipart/form-data">
  <input type="hidden" name="update_profile" value="1">

  <label for="first_name">Imię</label>
  <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>

  <label for="last_name">Nazwisko</label>
  <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>

    <div class="form-group phone-group">
  <label for="phone">Numer telefonu</label>
  <div class="phone-input-wrapper">
    <select id="country_code" name="country_code" required>
      <?php
      $codes = [
        '+48' => '🇵🇱 +48',
        '+49' => '🇩🇪 +49',
        '+44' => '🇬🇧 +44',
        '+420' => '🇨🇿 +420',
        '+421' => '🇸🇰 +421',
        '+33' => '🇫🇷 +33',
        '+39' => '🇮🇹 +39',
        '+1'  => '🇺🇸 +1'
      ];
      foreach ($codes as $c => $label) {
        $sel = ($user['country_code'] === $c) ? 'selected' : '';
        echo "<option value='$c' $sel>$label</option>";
      }
      ?>
    </select>
    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
  </div>
</div>


    <label for="address">Adres zamieszkania</label>
    <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required>


  <label for="avatar">Zmień awatar</label>
  <input type="file" id="avatar" name="avatar" accept="image/*">

  <label for="bio">Opis konta</label>
  <textarea id="bio" name="bio" rows="4" style="width:100%;border:none;border-radius:8px;background:rgba(255,255,255,0.9);font-size:1rem;color:#003b93;padding:12px;margin-bottom:14px;"><?php echo htmlspecialchars($user['bio']); ?></textarea>

  <button type="submit" class="save-btn">Zapisz zmiany</button>
</form>

      <hr style="border: none; border-top: 1px solid rgba(255,255,255,0.2); margin: 25px 0;">

      <h2 style="margin-bottom:10px;">Zmiana hasła</h2>
      <form method="POST">
        <input type="hidden" name="change_password" value="1">
        <label for="current_password">Aktualne hasło</label>
        <input type="password" id="current_password" name="current_password" required>

        <label for="new_password">Nowe hasło</label>
        <input type="password" id="new_password" name="new_password" required>

        <label for="confirm_password">Powtórz nowe hasło</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <button type="submit" class="save-btn">Zmień hasło</button>
      </form>

      <?php echo $message; ?>
    </div>
  </main>

  <footer>
    © 2025 AutoPart Battery — Panel użytkownika
  </footer>
</body>
</html>
