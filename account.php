<?php
require 'config.php';
$user = require_login($db);
$pageTitle = "Ustawienia konta";
include 'includes/header.php';

// Obsługa zapisu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
?>

<section class="account-settings">
  <h1>Ustawienia konta</h1>

  <div class="account-grid">
    <div class="account-left card">
      <img src="<?= htmlspecialchars($user['avatar'] ?: 'default-avatar.png') ?>" class="avatar-large" alt="Avatar">
      <form action="account.php" method="post" enctype="multipart/form-data" class="avatar-form">
        <label for="avatar">Zmień avatar:</label>
        <input type="file" id="avatar" name="avatar" accept="image/*">
        <button type="submit" class="btn-small">Zapisz</button>
      </form>
    </div>

    <div class="account-right card">
      <form action="account.php" method="post" class="account-form">
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
</section>

</section>

<?php include 'includes/footer.php'; ?>
