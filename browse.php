<?php
require 'config.php';
require_login($db);

$userEmail = $_SESSION['user_email'];

// Pobierz ID zalogowanego użytkownika
$stmtUser = $db->prepare("SELECT id, first_name, last_name, avatar FROM users WHERE email = ?");
$stmtUser->execute([$userEmail]);
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

// Dodawanie posta
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = trim($_POST['content'] ?? '');
    $mediaPath = null;

    if (!empty($_FILES['media']['name'])) {
        $uploadDir = 'uploads/posts/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileName = time() . '_' . basename($_FILES['media']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['media']['tmp_name'], $targetPath)) {
            $mediaPath = $targetPath;
        }
    }

    if ($content !== '' || $mediaPath) {
        $stmt = $db->prepare("INSERT INTO posts (user_id, content, media) VALUES (?, ?, ?)");
        $stmt->execute([$user['id'], $content, $mediaPath]);
    }

    header("Location: browse.php");
    exit;
}

// Pobierz wszystkie posty
$stmtPosts = $db->query("
    SELECT p.*, u.first_name, u.last_name, u.avatar
    FROM posts p
    JOIN users u ON u.id = p.user_id
    ORDER BY p.created_at DESC
");
$posts = $stmtPosts->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Przeglądanie - AutoPart Battery</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{box-sizing:border-box;margin:0;padding:0;font-family:"Segoe UI",Arial,sans-serif;}
body{background:linear-gradient(180deg,#0046ad 60%,#003b93 100%);color:white;min-height:100vh;display:flex;flex-direction:column;}
nav{background:rgba(255,255,255,0.1);backdrop-filter:blur(6px);display:flex;justify-content:space-between;align-items:center;padding:15px 40px;border-bottom:1px solid rgba(255,255,255,0.15);}
nav img{height:45px;}
.nav-links{display:flex;align-items:center;gap:20px;}
.nav-links a{color:#fff;text-decoration:none;font-weight:500;transition:0.3s;}
.nav-links a:hover{color:#dfe9ff;}
main{flex:1;padding:30px;display:flex;justify-content:center;}
.feed{width:100%;max-width:700px;}
.post-form{background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);border-radius:16px;padding:20px;margin-bottom:25px;backdrop-filter:blur(8px);}
.post-form textarea{width:100%;padding:12px;border:none;border-radius:8px;background:rgba(255,255,255,0.9);font-size:1rem;color:#003b93;resize:none;}
.post-form input[type=file]{margin-top:10px;margin-bottom:10px;color:white;}
.post-form button{background:white;color:#0046ad;border:none;border-radius:8px;padding:10px 20px;cursor:pointer;}
.post-form button:hover{background:#dfe9ff;}
.post{background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.15);border-radius:16px;padding:20px;margin-bottom:20px;backdrop-filter:blur(8px);}
.post-header{display:flex;align-items:center;gap:10px;margin-bottom:10px;}
.post-header img{width:50px;height:50px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,0.2);}
.post-header strong{font-size:1.1rem;}
.post-content{margin:10px 0;white-space:pre-wrap;}
.post-media{margin-top:10px;border-radius:10px;overflow:hidden;}
.post-media img, .post-media video{width:100%;border-radius:10px;max-height:500px;object-fit:cover;}
footer{text-align:center;padding:20px;font-size:0.85rem;opacity:0.75;}
</style>
</head>
<body>

<nav>
  <img src="logo.png" alt="AutoPart Battery">
  <div class="nav-links">
    <a href="dashboard.php"><i class="fa-solid fa-gauge"></i> Panel</a>
    <a href="browse.php"><i class="fa-solid fa-newspaper"></i> Przeglądanie</a>
    <a href="users.php"><i class="fa-solid fa-users"></i> Użytkownicy</a>
    <a href="account.php"><i class="fa-solid fa-gear"></i> Konto</a>
    <form method="post" action="logout.php" style="display:inline;">
      <button type="submit" style="background:white;color:#0046ad;border:none;border-radius:8px;padding:8px 14px;cursor:pointer;">Wyloguj</button>
    </form>
  </div>
</nav>

<main>
  <div class="feed">
    <!-- Formularz dodania posta -->
    <form class="post-form" method="post" enctype="multipart/form-data">
      <textarea name="content" rows="3" placeholder="Co słychać, <?php echo htmlspecialchars($user['first_name']); ?>?" required></textarea>
      <input type="file" name="media" accept="image/*,video/*">
      <button type="submit">Opublikuj</button>
    </form>

    <!-- Lista postów -->
    <?php foreach ($posts as $p): 
      $avatar = (!empty($p['avatar']) && file_exists($p['avatar'])) ? htmlspecialchars($p['avatar']) : 'default-avatar.png';
      $isVideo = $p['media'] && preg_match('/\.(mp4|mov|avi|mkv)$/i', $p['media']);
    ?>
      <div class="post">
        <div class="post-header">
          <img src="<?php echo $avatar; ?>" alt="Awatar">
          <div>
            <strong><a href="profile.php?id=<?php echo $p['user_id']; ?>" style="color:white;text-decoration:none;"><?php echo htmlspecialchars($p['first_name'].' '.$p['last_name']); ?></a></strong><br>
            <small><?php echo date('d.m.Y H:i', strtotime($p['created_at'])); ?></small>
          </div>
        </div>
        <div class="post-content"><?php echo nl2br(htmlspecialchars($p['content'])); ?></div>

        <?php if ($p['media']): ?>
        <div class="post-media">
          <?php if ($isVideo): ?>
            <video controls>
              <source src="<?php echo htmlspecialchars($p['media']); ?>" type="video/mp4">
            </video>
          <?php else: ?>
            <img src="<?php echo htmlspecialchars($p['media']); ?>" alt="Post media">
          <?php endif; ?>
        </div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
</main>

<footer>© 2025 AutoPart Battery — Przeglądanie postów</footer>

</body>
</html>
