<?php
require 'config.php';
$user = require_login($db);
$pageTitle = "Przeglądanie społeczności";
include 'includes/header.php';

// dodanie posta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content'])) {
    $content = trim($_POST['content']);
    $mediaPath = null;

    if (!empty($_FILES['media']['name'])) {
        $uploadDir = 'uploads/posts/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $fileName = time().'_'.basename($_FILES['media']['name']);
        $target = $uploadDir.$fileName;
        if (move_uploaded_file($_FILES['media']['tmp_name'], $target)) {
            $mediaPath = $target;
        }
    }

    if ($content || $mediaPath) {
        $stmt = $db->prepare("INSERT INTO posts (user_id, content, media, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$user['id'], $content, $mediaPath]);
    }
}

// pobranie postów
$stmt = $db->query("
    SELECT p.*, u.first_name, u.last_name, u.avatar 
    FROM posts p 
    JOIN users u ON u.id = p.user_id 
    ORDER BY p.created_at DESC
");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="browse">
  <h1>Posty społeczności</h1>

  <form action="browse.php" method="post" enctype="multipart/form-data" class="card post-form">
    <textarea name="content" placeholder="Co słychać, <?= htmlspecialchars($user['first_name']) ?>?" rows="3"></textarea>
    <input type="file" name="media" accept="image/*,video/*">
    <button type="submit">Opublikuj</button>
  </form>

  <?php foreach ($posts as $p): ?>
    <?php
      $avatar = $p['avatar'] ?: 'default-avatar.png';

      // Pobierz liczbę reakcji
      $r = $db->prepare("SELECT type, COUNT(*) AS count FROM reactions WHERE post_id=? GROUP BY type");
      $r->execute([$p['id']]);
      $reactions = $r->fetchAll(PDO::FETCH_KEY_PAIR);
      $total = array_sum($reactions);
    ?>
    <div class="card post" id="post-<?= $p['id'] ?>">
      <div class="post-header">
        <img src="<?= htmlspecialchars($avatar) ?>" class="avatar-small">
        <div>
          <strong><?= htmlspecialchars($p['first_name'].' '.$p['last_name']) ?></strong><br>
          <small><?= date('d.m.Y H:i', strtotime($p['created_at'])) ?></small>
        </div>
      </div>

      <div class="post-content"><?= nl2br(htmlspecialchars($p['content'])) ?></div>

      <?php if ($p['media']): ?>
        <?php if (preg_match('/\.(mp4|webm)$/i', $p['media'])): ?>
          <video src="<?= htmlspecialchars($p['media']) ?>" controls class="post-media"></video>
        <?php else: ?>
          <img src="<?= htmlspecialchars($p['media']) ?>" class="post-media">
        <?php endif; ?>
      <?php endif; ?>

      <div class="reactions" id="reactions-<?= $p['id'] ?>">
        <div class="reaction-buttons">
          <button class="reaction-btn" data-type="like" data-id="<?= $p['id'] ?>">👍</button>
          <button class="reaction-btn" data-type="love" data-id="<?= $p['id'] ?>">❤️</button>
          <button class="reaction-btn" data-type="haha" data-id="<?= $p['id'] ?>">😂</button>
          <button class="reaction-btn" data-type="wow" data-id="<?= $p['id'] ?>">😮</button>
          <button class="reaction-btn" data-type="sad" data-id="<?= $p['id'] ?>">😢</button>
          <button class="reaction-btn" data-type="angry" data-id="<?= $p['id'] ?>">😡</button>
        </div>
        <div class="reaction-stats">
          <?php if ($total > 0): ?>
            <span class="reaction-total"><?= $total ?> reakcji:</span>
            <?php foreach ($reactions as $type => $count): ?>
              <span class="reaction-type"><?= htmlspecialchars($type) ?> (<?= $count ?>)</span>
            <?php endforeach; ?>
          <?php else: ?>
            <span class="reaction-total">Brak reakcji</span>
          <?php endif; ?>
        </div>
      </div>

      <div class="comments" id="comments-<?= $p['id'] ?>"></div>

      <form class="comment-form" data-id="<?= $p['id'] ?>">
        <input type="text" name="content" placeholder="Napisz komentarz..." required>
      </form>
    </div>
  <?php endforeach; ?>
</section>

<script>
// Reakcje z licznikami
document.querySelectorAll('.reaction-btn').forEach(btn=>{
  btn.addEventListener('click',()=>{
    fetch('react.php',{
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body:`post_id=${btn.dataset.id}&type=${btn.dataset.type}`
    })
    .then(()=>updateReactions(btn.dataset.id));
  });
});

function updateReactions(postId){
  fetch('get_reactions.php?post_id='+postId)
  .then(r=>r.json())
  .then(data=>{
    const target = document.querySelector('#reactions-'+postId+' .reaction-stats');
    if(!target) return;
    if(data.total === 0){
      target.innerHTML = '<span class="reaction-total">Brak reakcji</span>';
    } else {
      target.innerHTML = `<span class="reaction-total">${data.total} reakcji:</span>` +
        Object.entries(data.types).map(([t,c])=>`<span class="reaction-type">${t} (${c})</span>`).join(' ');
    }
  });
}

// Komentarze AJAX
document.querySelectorAll('.comment-form').forEach(form=>{
  const postId = form.dataset.id;
  loadComments(postId);
  form.addEventListener('submit',e=>{
    e.preventDefault();
    const content = form.querySelector('input[name="content"]').value;
    fetch('comment.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body:`post_id=${postId}&content=${encodeURIComponent(content)}`})
    .then(()=>{form.reset();loadComments(postId);});
  });
});

function loadComments(postId){
  fetch('fetch_comments.php?post_id='+postId)
  .then(r=>r.json())
  .then(data=>{
    const c = document.getElementById('comments-'+postId);
    c.innerHTML = data.map(d=>`
      <div class="comment">
        <strong>${d.first_name} ${d.last_name}</strong>
        <small>${new Date(d.created_at).toLocaleString()}</small><br>
        ${d.content}
      </div>`).join('');
  });
}
</script>

<?php include 'includes/footer.php'; ?>
