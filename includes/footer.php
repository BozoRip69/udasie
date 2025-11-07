    </main>

    <footer class="site-footer">
      <div class="footer-content">
        <p>© <?= date('Y') ?> AutoPart Battery Community</p>
        <nav class="footer-nav">
          <a href="support.php"><i class="fa-solid fa-headset"></i> Pomoc techniczna</a>
        </nav>
      </div>
    </footer>

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

    <style>
    .site-footer {
      background: #f8f9fa;
      border-top: 1px solid #e2e8f0;
      padding: 12px 20px;
      text-align: center;
      font-size: 0.95rem;
      color: #444;
    }
    .footer-content {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 16px;
      flex-wrap: wrap;
    }
    .footer-nav a {
      color: #007bff;
      text-decoration: none;
      font-weight: 500;
      transition: color 0.2s;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }
    .footer-nav a:hover {
      color: #0056b3;
    }

    /* Tryb ciemny */
    body.dark .site-footer {
      background: #0f172a;
      border-top: 1px solid #1e293b;
      color: #e5e7eb;
    }
    body.dark .footer-nav a {
      color: #60a5fa;
    }
    body.dark .footer-nav a:hover {
      color: #93c5fd;
    }
    </style>

  </body>
</html>
