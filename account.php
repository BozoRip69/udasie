<?php
require 'config.php';
$user = require_login($db);
$pageTitle = "Ustawienia konta";
include 'includes/header.php';

// =========================
// 🟢 Aktualizacja danych profilu
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $first = trim($_POST['first_name']);
    $last = trim($_POST['last_name']);
    $country = $_POST['country_code'];
    $phone = preg_replace('/\D/', '', $_POST['phone']);
    $address = trim($_POST['address']);
    $bio = trim($_POST['bio']);

    $avatarPath = $user['avatar'];
    if (!empty($_FILES['avatar']['name'])) {
        $uploadDir = 'uploads/avatars/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $fileName = time().'_'.basename($_FILES['avatar']['name']);
        $target = $uploadDir.$fileName;
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target)) {
            $avatarPath = $target;
        }
    }

    $stmt = $db->prepare("UPDATE users SET first_name=?, last_name=?, country_code=?, phone=?, address=?, avatar=?, bio=? WHERE email=?");
    $stmt->execute([$first, $last, $country, $phone, $address, $avatarPath, $bio, $user['email']]);

    header("Location: account.php?updated=1");
    exit;
}

// =========================
// 🔵 Zmiana hasła
// =========================
$passwordMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Sprawdź aktualne hasło
    $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user['id']]);
    $hash = $stmt->fetchColumn();

    if (!$hash || !password_verify($currentPassword, $hash)) {
        $passwordMessage = "<p class='error'>❌ Nieprawidłowe obecne hasło.</p>";
    } elseif (strlen($newPassword) < 6) {
        $passwordMessage = "<p class='error'>❌ Nowe hasło musi mieć co najmniej 6 znaków.</p>";
    } elseif ($newPassword !== $confirmPassword) {
        $passwordMessage = "<p class='error'>❌ Hasła nie są identyczne.</p>";
    } else {
        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE users SET password=?, last_password_change=NOW() WHERE id=?");
        $stmt->execute([$newHash, $user['id']]);
        $passwordMessage = "<p class='success'>✅ Hasło zostało pomyślnie zmienione.</p>";
    }
}
?>

<section class="account-settings">
  <h1>Ustawienia konta</h1>

  <div class="account-grid">
    <div class="account-left card">
      <img src="<?= htmlspecialchars($user['avatar'] ?: 'default-avatar.png') ?>" class="avatar-large" alt="Avatar">
      <form action="account.php" method="post" enctype="multipart/form-data" class="avatar-form">
        <label for="avatar">Zmień avatar:</label>
        <input type="file" id="avatar" name="avatar" accept="image/*">
        <button type="submit" name="update_profile" class="btn-small">Zapisz</button>
      </form>
    </div>

    <div class="account-right card">
      <form action="account.php" method="post" class="account-form">
        <input type="hidden" name="update_profile" value="1">

        <label>Imię</label>
        <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>">

        <label>Nazwisko</label>
        <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>">

        <label>Kierunkowy i numer telefonu</label>
        <div class="phone-input">
          <select name="country_code" class="country-code">
            <option value="+48" <?= $user['country_code']=='+48'?'selected':'' ?>>🇵🇱 +48</option>
            <option value="+49" <?= $user['country_code']=='+49'?'selected':'' ?>>🇩🇪 +49</option>
            <option value="+44" <?= $user['country_code']=='+44'?'selected':'' ?>>🇬🇧 +44</option>
            <option value="+420" <?= $user['country_code']=='+420'?'selected':'' ?>>🇨🇿 +420</option>
          </select>
          <input type="text" name="phone" maxlength="9" value="<?= htmlspecialchars($user['phone']) ?>" pattern="[0-9]{9}" placeholder="np. 501234567" required>
        </div>

        <label>Adres zamieszkania</label>
        <input type="text" name="address" value="<?= htmlspecialchars($user['address']) ?>">

        <label>Opis konta (bio)</label>
        <textarea name="bio" rows="3" placeholder="Napisz coś o sobie..."><?= htmlspecialchars($user['bio']) ?></textarea>

        <button type="submit" class="btn">Zapisz zmiany</button>
      </form>
    </div>
  </div>

  <!-- 🔵 Informacje o bezpieczeństwie i zmiana hasła -->
  <div class="account-info card" style="margin-top: 2rem;">
    <h2>Bezpieczeństwo konta</h2>

    <p><strong>Ostatnie logowanie:</strong>
      <?= $user['last_login'] ? date('d.m.Y H:i', strtotime($user['last_login'])) : 'Brak danych' ?>
    </p>

    <p><strong>Ostatnia zmiana hasła:</strong>
      <?= $user['last_password_change'] ? date('d.m.Y H:i', strtotime($user['last_password_change'])) : 'Brak danych' ?>
    </p>

    <hr>

    <h3>Zmień hasło</h3>
    <?= $passwordMessage ?>

    <form method="post" action="account.php" class="password-form">
      <input type="hidden" name="change_password" value="1">

      <label>Obecne hasło</label>
      <input type="password" name="current_password" required>

      <label>Nowe hasło</label>
      <input type="password" name="new_password" required minlength="6">

      <label>Powtórz nowe hasło</label>
      <input type="password" name="confirm_password" required minlength="6">

      <button type="submit" class="btn-small">Zmień hasło</button>
    </form>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
