<?php
require 'config.php';
require_login($db); // jeśli nieprawidłowa sesja -> przekierowanie do login.html
$user = htmlspecialchars($_SESSION['user_email']);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AutoPart Battery - Panel użytkownika</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Segoe UI", Arial, sans-serif; }

    body {
      background: linear-gradient(180deg, #0046ad 60%, #003b93 100%);
      color: white;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* ======= NAVBAR ======= */
    nav {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(6px);
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 40px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.15);
      box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }

    nav img {
      height: 45px;
    }

    .nav-links {
      display: flex;
      align-items: center;
      gap: 25px;
    }

    .nav-links a {
      color: #fff;
      text-decoration: none;
      font-weight: 500;
      transition: 0.3s;
    }

    .nav-links a:hover {
      color: #dfe9ff;
    }

    .user-info {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 0.95rem;
    }

    .logout-btn {
      background: white;
      color: #0046ad;
      border: none;
      border-radius: 8px;
      padding: 8px 16px;
      font-size: 0.9rem;
      cursor: pointer;
      transition: 0.3s;
    }

    .logout-btn:hover {
      background: #dfe9ff;
    }

    /* ======= MAIN ======= */
    main {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      padding: 60px 20px;
    }

    .dashboard-container {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(8px);
      border-radius: 16px;
      padding: 40px 50px;
      width: 100%;
      max-width: 800px;
      text-align: center;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
      animation: fadeIn 0.8s ease forwards;
    }

    .dashboard-container h1 {
      font-size: 2rem;
      margin-bottom: 20px;
    }

    .dashboard-container p {
      font-size: 1rem;
      opacity: 0.9;
      margin-bottom: 25px;
    }

    .info-box {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 10px;
      padding: 20px;
      text-align: left;
      margin-top: 20px;
    }

    .info-box h3 {
      margin-bottom: 10px;
      font-size: 1.1rem;
      color: #dfe9ff;
    }

    .info-box p {
      font-size: 0.95rem;
      line-height: 1.5;
    }

    footer {
      text-align: center;
      padding: 20px;
      font-size: 0.85rem;
      opacity: 0.7;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 600px) {
      nav { padding: 15px 20px; flex-direction: column; gap: 10px; }
      .nav-links { flex-wrap: wrap; justify-content: center; gap: 15px; }
      .dashboard-container { padding: 25px 20px; }
    }
  </style>
</head>
<body>

  <!-- ======= GÓRNE MENU ======= -->
  <nav>
    <img src="logo.png" alt="AutoPart Battery">
    <div class="nav-links">
      <a href="browse.php"><i class="fa-solid fa-newspaper"></i> Przeglądanie</a>
      <a href="users.php"><i class="fa-solid fa-users"></i> Użytkownicy</a>  
      <a href="dashboard.php"><i class="fa-solid fa-gauge"></i> Panel główny</a>
      <a href="account.php"><i class="fa-solid fa-gear"></i> Ustawienia konta</a>
      <div class="user-info">
        <i class="fa-solid fa-user"></i> <?php echo $user; ?>
        <form method="post" action="logout.php" style="display:inline;">
          <button type="submit" class="logout-btn">Wyloguj</button>
        </form>
      </div>
    </div>
  </nav>

  <!-- ======= ZAWARTOŚĆ STRONY ======= -->
  <main>
    <div class="dashboard-container">
      <h1>Witaj, <?php echo $user; ?>!</h1>
      <p>Miło Cię znowu widzieć w panelu AutoPart Battery ⚡</p>

      <div class="info-box">
        <h3><i class="fa-solid fa-circle-info"></i> Informacje ogólne</h3>
        <p>
          To jest Twoja strona główna. W przyszłości pojawi się tu wyszukiwarka pojazdów po numerze VIN zintegrowana z CEPiK.  
          Możesz przejść do ustawień konta, zmienić hasło lub wylogować się przyciskiem w prawym górnym rogu.
        </p>
      </div>

      <div class="info-box">
        <h3><i class="fa-solid fa-bolt"></i> Nadchodzące funkcje</h3>
        <ul style="margin-top:10px; text-align:left; line-height:1.6;">
          <li>🔍 Wyszukiwarka pojazdów po VIN</li>
          <li>📊 Historia zapytań</li>
          <li>⚙️ Edycja profilu użytkownika</li>
          <li>📱 Responsywny panel mobilny</li>
        </ul>
      </div>
    </div>
  </main>

  <footer>
    © 2025 AutoPart Battery — Panel użytkownika
  </footer>

</body>
</html>
