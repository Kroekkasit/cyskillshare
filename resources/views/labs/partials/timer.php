<script>
(() => {
  const timerEl = document.querySelector('[data-lab-timer]');
  if (!timerEl) return;

  const display = timerEl.querySelector('[data-timer-display]');
  const statusUrl = timerEl.getAttribute('data-status-url');
  const pollSeconds = parseInt(timerEl.getAttribute('data-poll-seconds') || '2', 10) * 1000;
  // Server-authoritative remaining seconds (never trust browser clock vs MySQL).
  let remaining = parseInt(timerEl.getAttribute('data-seconds-remaining') || '0', 10);
  if (Number.isNaN(remaining)) remaining = 0;

  function formatSeconds(total) {
    if (total == null || total < 0) return '00:00';
    const h = Math.floor(total / 3600);
    const m = Math.floor((total % 3600) / 60);
    const s = total % 60;
    if (h > 0) return `${h}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
    return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
  }

  function paint() {
    if (display) display.textContent = formatSeconds(remaining);
    timerEl.classList.toggle('is-expired', remaining <= 0);
  }

  paint();
  setInterval(() => {
    if (remaining > 0) remaining -= 1;
    paint();
  }, 1000);

  async function pollStatus() {
    if (!statusUrl) return;
    try {
      const res = await fetch(statusUrl, { headers: { Accept: 'application/json' } });
      if (!res.ok) return;
      const data = await res.json();
      if (typeof data.seconds_remaining === 'number') {
        remaining = data.seconds_remaining;
        paint();
      }
      const badge = document.getElementById('lab-provision-badge');
      if (badge && data.provision_state) badge.textContent = data.provision_state;
      if (data.provision_state === 'ready' && document.querySelector('[data-lab-provisioning]')) {
        window.location.reload();
      }
    } catch { /* ignore */ }
  }

  if (statusUrl) {
    pollStatus();
    setInterval(pollStatus, pollSeconds);
  }

  const provPanel = document.querySelector('[data-lab-provisioning]');
  if (provPanel) {
    const bar = provPanel.querySelector('[data-provision-bar]');
    let pct = 10;
    setInterval(() => {
      pct = Math.min(95, pct + 5);
      if (bar) bar.style.width = pct + '%';
    }, 800);
  }
})();
</script>
