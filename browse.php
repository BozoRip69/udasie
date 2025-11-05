<?php
require 'config.php';
require_login($db);

// Dane zalogowanego użytkownika
$userStmt = $db->prepare("SELECT id, first_name, last_name, avatar FROM users WHERE email = ?");
$userStmt->execute([$_SESSION['user_email']]);
$currentUser = $userStmt->fetch(PDO::FETCH_ASSOC);
if (!$currentUser) {
    header("Location: login.html");
    exit;
}

// Obsługa dodawania posta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_post'])) {
    $content = trim($_POST['content'] ?? '');
    $mediaPath = null;

    if (!empty($_FILES['media']['name'])) {
        $uploadDir = 'uploads/posts/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $safeName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', basename($_FILES['media']['name']));
        $fileName = time() . '_' . $safeName;
        $targetPath = $uploadDir . $fileName;

        $allowed = ['image/jpeg','image/png','image/gif','image/webp','video/mp4','video/quicktime','video/x-msvideo','video/x-matroska'];
        $mime = mime_content_type($_FILES['media']['tmp_name']);
        if (in_array($mime, $allowed) && move_uploaded_file($_FILES['media']['tmp_name'], $targetPath)) {
            $mediaPath = $targetPath;
        }
    }

    if ($content !== '' || $mediaPath) {
        $ins = $db->prepare("INSERT INTO posts (user_id, content, media) VALUES (?, ?, ?)");
        $ins->execute([$currentUser['id'], $content, $mediaPath]);
    }

    header("Location: browse.php");
    exit;
}

// Pobranie postów z licznikami
$postsStmt = $db->query("
    SELECT 
        p.id, p.user_id, p.content, p.media, p.created_at,
        u.first_name, u.last_name, u.avatar,
        (SELECT COUNT(*) FROM reactions r WHERE r.post_id = p.id) AS reactions_total,
        (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) AS comments_total,
        (SELECT COUNT(*) FROM reactions r WHERE r.post_id = p.id AND r.type='like')  AS like_cnt,
        (SELECT COUNT(*) FROM reactions r WHERE r.post_id = p.id AND r.type='love')  AS love_cnt,
        (SELECT COUNT(*) FROM reactions r WHERE r.post_id = p.id AND r.type='haha')  AS haha_cnt,
        (SELECT COUNT(*) FROM reactions r WHERE r.post_id = p.id AND r.type='wow')   AS wow_cnt,
        (SELECT COUNT(*) FROM reactions r WHERE r.post_id = p.id AND r.type='sad')   AS sad_cnt,
        (SELECT COUNT(*) FROM reactions r WHERE r.post_id = p.id AND r.type='angry') AS angry_cnt
    FROM posts p
    JOIN users u ON u.id = p.user_id
    ORDER BY p.created_at DESC
");
$posts = $postsStmt->fetchAll(PDO::FETCH_ASSOC);

// Reakcje bieżącego usera (do podświetlania)
$reactedStmt = $db->prepare("SELECT post_id, type FROM reactions WHERE user_id = ?");
$reactedStmt->execute([$currentUser['id']]);
$userReactions = [];
foreach ($reactedStmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
    $userReactions[(int)$r['post_id']] = $r['type'];
}

function avatar_src($path) {
    return (!empty($path) && file_exists($path)) ? htmlspecialchars($path) : 'default-avatar.png';
}
function is_video($path) {
    return $path && preg_match('/\.(mp4|mov|avi|mkv)$/i', $path);
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Przeglądanie — AutoPart Battery</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{box-sizing:border-box;margin:0;padding:0;font-family:"Segoe UI",Arial,sans-serif;}
body{background:linear-gradient(180deg,#0046ad 60%,#003b93 100%);color:white;min-height:100vh;display:flex;flex-direction:column;}
nav{background:rgba(255,255,255,0.1);backdrop-filter:blur(6px);display:flex;justify-content:space-between;align-items:center;padding:15px 40px;border-bottom:1px solid rgba(255,255,255,0.15);}
nav img{height:45px;}
.nav-links{display:flex;align-items:center;gap:18px;}
.nav-links a{color:#fff;text-decoration:none;font-weight:500;transition:.2s;}
.nav-links a:hover{color:#dfe9ff;}
.nav-logout{background:#fff;color:#0046ad;border:0;border-radius:8px;padding:8px 14px;cursor:pointer;}
main{flex:1;padding:30px;display:flex;justify-content:center;}
.feed{width:100%;max-width:760px;}
.card{background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);border-radius:16px;padding:18px 20px;margin-bottom:22px;backdrop-filter:blur(8px);box-shadow:0 8px 25px rgba(0,0,0,0.3);}
.post-form textarea{width:100%;padding:12px;border:none;border-radius:8px;background:rgba(255,255,255,0.9);font-size:1rem;color:#003b93;resize:vertical;min-height:70px;}
.post-form input[type=file]{margin:10px 0;color:white;}
.post-form button{background:#fff;color:#0046ad;border:none;border-radius:8px;padding:10px 16px;font-weight:600;cursor:pointer;}
.post-form button:hover{background:#dfe9ff;}
.post{padding:18px 20px;}
.post-header{display:flex;align-items:center;gap:12px;}
.post-header img{width:52px;height:52px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,0.2);}
.post-header .meta{display:flex;flex-direction:column;}
.post-header .meta strong a{color:#fff;text-decoration:none;}
.post-content{margin:12px 0;white-space:pre-wrap;line-height:1.5;}
.post-media{margin-top:8px;border-radius:12px;overflow:hidden;}
.post-media img,.post-media video{width:100%;max-height:560px;border-radius:12px;object-fit:cover;}
.toolbar{display:flex;align-items:center;gap:10px;margin-top:8px;flex-wrap:wrap;}
.reaction-btn{background:rgba(255,255,255,0.18);border:none;border-radius:24px;padding:6px 10px;cursor:pointer;color:#fff;font-size:.95rem;transition:.2s;}
.reaction-btn:hover{background:rgba(255,255,255,0.3);}
.reaction-btn.active{box-shadow:0 0 0 2px rgba(255,255,255,0.45) inset;}
.counts{margin-left:auto;opacity:.9;font-size:.9rem;}
.post-footer{margin-top:10px;border-top:1px solid rgba(255,255,255,0.15);padding-top:10px;}
.comment{font-size:.95rem;margin-bottom:8px;}
.comment strong{color:#fff;}
.comment small{opacity:.85;}
.comment form input{width:100%;padding:10px;border:none;border-radius:8px;background:rgba(255,255,255,0.9);color:#003b93;margin-top:8px;}
footer{text-align:center;padding:18px;opacity:.75;font-size:.9rem;}
@media (max-width:600px){nav{padding:12px 16px;flex-direction:column;gap:8px;}}
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
      <button type="submit" class="nav-logout">Wyloguj</button>
    </form>
  </div>
</nav>

<main>
  <div class="feed">
    <!-- Formularz dodania posta -->
    <div class="card post-form">
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="add_post" value="1">
        <textarea name="content" placeholder="Co słychać, <?php echo htmlspecialchars($currentUser['first_name']); ?>?"></textarea>
        <input type="file" name="media" accept="image/*,video/*">
        <button type="submit"><i class="fa-solid fa-paper-plane"></i> Opublikuj</button>
      </form>
    </div>

    <!-- Lista postów -->
    <?php foreach ($posts as $p): 
      $avatar = avatar_src($p['avatar']);
      $media = $p['media'] ? htmlspecialchars($p['media']) : null;
      $youReacted = $userReactions[$p['id']] ?? null;
    ?>
      <div class="card post" id="post-<?php echo (int)$p['id']; ?>">
        <div class="post-header">
          <img src="<?php echo $avatar; ?>" alt="Awatar">
          <div class="meta">
            <strong><a href="profile.php?id=<?php echo (int)$p['user_id']; ?>"><?php echo htmlspecialchars($p['first_name'].' '.$p['last_name']); ?></a></strong>
            <small><?php echo date('d.m.Y H:i', strtotime($p['created_at'])); ?></small>
          </div>
        </div>

        <?php if ($p['content']): ?>
          <div class="post-content"><?php echo nl2br(htmlspecialchars($p['content'])); ?></div>
        <?php endif; ?>

        <?php if ($media): ?>
          <div class="post-media">
            <?php if (is_video($media)): ?>
              <video controls><source src="<?php echo $media; ?>"></video>
            <?php else: ?>
              <img src="<?php echo $media; ?>" alt="Media">
            <?php endif; ?>
          </div>
        <?php endif; ?>

        <!-- Reakcje -->
        <div class="toolbar">
          <?php
            $btns = ['like'=>'👍','love'=>'❤️','haha'=>'😂','wow'=>'😮','sad'=>'😢','angry'=>'😡'];
            $counts = [
              'like'=>(int)$p['like_cnt'],'love'=>(int)$p['love_cnt'],'haha'=>(int)$p['haha_cnt'],
              'wow'=>(int)$p['wow_cnt'],'sad'=>(int)$p['sad_cnt'],'angry'=>(int)$p['angry_cnt'],
            ];
          ?>
          <?php foreach ($btns as $type => $emoji): ?>
            <button 
              class="reaction-btn<?php echo ($youReacted === $type ? ' active' : ''); ?>" 
              data-type="<?php echo $type; ?>" 
              data-id="<?php echo (int)$p['id']; ?>">
              <?php echo $emoji; ?><?php if ($type==='like') echo ' '.(int)$p['reactions_total']; ?>
            </button>
          <?php endforeach; ?>
          <div class="counts">💬 <?php echo (int)$p['comments_total']; ?></div>
        </div>

        <!-- Komentarze -->
        <div class="post-footer">
          <div class="comments" id="comments-<?php echo (int)$p['id']; ?>"></div>
          <form class="comment-form" data-id="<?php echo (int)$p['id']; ?>" autocomplete="off">
            <input type="text" name="content" placeholder="Napisz komentarz...">
          </form>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</main>

<footer>© 2025 AutoPart Battery — Przeglądanie postów</footer>

<script>
// Reakcje (AJAX)
document.querySelectorAll('.reaction-btn').forEach(btn=>{
  btn.addEventListener('click', ()=>{
    const postId = btn.dataset.id;
    const type   = btn.dataset.type;
    fetch('react.php', {
      method: 'POST',
      headers: {'Content-Type':'application/x-www-form-urlencoded'},
      body: `post_id=${encodeURIComponent(postId)}&type=${encodeURIComponent(type)}`
    }).then(()=> location.reload());
  });
});

// Komentarze — ładowanie i dodawanie
document.querySelectorAll('.comment-form').forEach(form=>{
  const postId = form.dataset.id;
  const box    = document.getElementById('comments-'+postId);

  function loadComments() {
    fetch('fetch_comments.php?post_id='+encodeURIComponent(postId))
      .then(r=>r.json())
      .then(list=>{
        box.innerHTML = list.map(c=>{
          const date = new Date(c.created_at);
          return `
            <div class="comment">
              <strong>${escapeHtml(c.first_name)} ${escapeHtml(c.last_name)}</strong>
              <small> — ${date.toLocaleString()}</small><br>
              ${escapeHtml(c.content)}
            </div>
          `;
        }).join('');
      });
  }

  form.addEventListener('submit', e=>{
    e.preventDefault();
    const input = form.querySelector('input[name="content"]');
    const txt = input.value.trim();
    if (!txt) return;
    fetch('comment.php', {
      method: 'POST',
      headers: {'Content-Type':'application/x-www-form-urlencoded'},
      body: `post_id=${encodeURIComponent(postId)}&content=${encodeURIComponent(txt)}`
    }).then(()=>{
      input.value = '';
      loadComments();
    });
  });

  form.addEventListener('keydown', e=>{
    if (e.key === 'Enter') {
      e.preventDefault();
      form.dispatchEvent(new Event('submit'));
    }
  });

  loadComments();
});

function escapeHtml(str){
  return (str ?? '').toString()
    .replaceAll('&','&amp;')
    .replaceAll('<','&lt;')
    .replaceAll('>','&gt;')
    .replaceAll('"','&quot;')
    .replaceAll("'",'&#039;');
}
</script>
</body>
</html>
