    </main>
<footer>© <?= date('Y') ?> AutoPart Battery Community</footer>
<script src="assets/script.js"></script>
<script>
async function updateBadges() {
  try {
    const res = await fetch('get_unread.php');
    const data = await res.json();

    const msgBadge = document.getElementById('msg-count');
    const notifBadge = document.getElementById('notif-count');

    if (data.messages > 0) {
      msgBadge.textContent = data.messages;
      msgBadge.style.display = 'flex';
    } else {
      msgBadge.style.display = 'none';
    }

    if (data.notifications > 0) {
      notifBadge.textContent = data.notifications;
      notifBadge.style.display = 'flex';
    } else {
      notifBadge.style.display = 'none';
    }
  } catch (e) {
    console.error('Błąd przy pobieraniu liczników:', e);
  }
}

updateBadges();
setInterval(updateBadges, 5000);
</script>

</body>
</html>
