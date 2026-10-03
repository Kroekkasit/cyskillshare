(() => {
  const STORAGE_KEY = 'cyskillshare-theme';

  function resolveTheme(value) {
    if (value === 'light' || value === 'dark') {
      return value;
    }
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  }

  function applyTheme(theme) {
    const resolved = resolveTheme(theme);
    document.documentElement.setAttribute('data-theme', resolved);

    document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
      const next = resolved === 'dark' ? 'light' : 'dark';
      btn.setAttribute('aria-label', `Switch to ${next} theme`);
      const label = btn.querySelector('[data-theme-label]');
      const hint = btn.querySelector('[data-theme-hint]');
      if (label) {
        label.textContent = resolved === 'dark' ? 'Dark theme' : 'Light theme';
      }
      if (hint) {
        hint.textContent = `Switch to ${next}`;
      }
    });
  }

  function currentStored() {
    try {
      return localStorage.getItem(STORAGE_KEY);
    } catch {
      return null;
    }
  }

  function setTheme(theme) {
    try {
      localStorage.setItem(STORAGE_KEY, theme);
    } catch {
      // ignore quota / private mode
    }
    applyTheme(theme);
  }

  // Apply early (layout scripts also include an inline head bootstrap)
  applyTheme(currentStored());

  document.addEventListener('DOMContentLoaded', () => {
    document.documentElement.classList.add('js');

    document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
      btn.addEventListener('click', () => {
        const current = document.documentElement.getAttribute('data-theme') || 'light';
        setTheme(current === 'dark' ? 'light' : 'dark');
      });
    });

    // Keep in sync if OS preference changes and user has not forced a choice
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
      if (!currentStored()) {
        applyTheme(null);
      }
    });
  });
})();
