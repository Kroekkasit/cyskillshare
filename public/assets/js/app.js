(() => {
  const STORAGE_KEY = 'cyskillshare-theme';

  function resolveTheme(value) {
    if (value === 'light' || value === 'dark') {
      return value;
    }
    // Dark Cyber Academy default when OS preference unavailable
    return window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
  }

  function applyTheme(theme) {
    const resolved = resolveTheme(theme);
    document.documentElement.setAttribute('data-theme', resolved);

    document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
      const next = resolved === 'dark' ? 'light' : 'dark';
      btn.setAttribute('aria-label', `Switch to ${next} theme`);
      const label = btn.querySelector('[data-theme-label]');
      const hint = btn.querySelector('[data-theme-hint]');
      if (label) label.textContent = resolved === 'dark' ? 'Dark theme' : 'Light theme';
      if (hint) hint.textContent = `Switch to ${next}`;
    });
  }

  function currentStored() {
    try { return localStorage.getItem(STORAGE_KEY); } catch { return null; }
  }

  function setTheme(theme) {
    try { localStorage.setItem(STORAGE_KEY, theme); } catch { /* ignore */ }
    applyTheme(theme);
  }

  applyTheme(currentStored());

  document.addEventListener('DOMContentLoaded', () => {
    document.documentElement.classList.add('js');

    document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
      btn.addEventListener('click', () => {
        const current = document.documentElement.getAttribute('data-theme') || 'dark';
        setTheme(current === 'dark' ? 'light' : 'dark');
      });
    });

    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
      if (!currentStored()) applyTheme(null);
    });

    // Copy code blocks
    document.querySelectorAll('[data-copy-code]').forEach((btn) => {
      btn.addEventListener('click', async () => {
        const block = btn.closest('.code-block');
        const code = block ? block.querySelector('code') : null;
        if (!code) return;
        try {
          await navigator.clipboard.writeText(code.textContent || '');
          const original = btn.textContent;
          btn.textContent = 'Copied';
          setTimeout(() => { btn.textContent = original; }, 1200);
        } catch {
          btn.textContent = 'Failed';
        }
      });
    });

    // Report dialog
    const dialog = document.getElementById('report-dialog');
    const typeInput = document.getElementById('report-target-type');
    const idInput = document.getElementById('report-target-id');
    document.querySelectorAll('[data-report]').forEach((btn) => {
      btn.addEventListener('click', () => {
        if (!dialog || !typeInput || !idInput) return;
        typeInput.value = btn.getAttribute('data-type') || '';
        idInput.value = btn.getAttribute('data-id') || '';
        if (typeof dialog.showModal === 'function') dialog.showModal();
      });
    });
    document.querySelectorAll('[data-close-report]').forEach((btn) => {
      btn.addEventListener('click', () => {
        if (dialog && typeof dialog.close === 'function') dialog.close();
      });
    });

    // Mobile channel drawer
    const drawer = document.getElementById('channel-drawer');
    document.querySelectorAll('[data-channel-drawer-toggle]').forEach((btn) => {
      btn.addEventListener('click', () => {
        if (!drawer) return;
        const open = drawer.classList.toggle('is-open');
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
      });
    });
  });
})();
