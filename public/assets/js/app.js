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

    // Markdown editor toolbar inserts
    document.querySelectorAll('[data-md-insert]').forEach((btn) => {
      btn.addEventListener('click', () => {
        const ta = document.querySelector('.writeup-content-input');
        if (!ta) return;
        const insert = btn.getAttribute('data-md-insert') || '';
        const start = ta.selectionStart ?? ta.value.length;
        const end = ta.selectionEnd ?? start;
        const before = ta.value.slice(0, start);
        const after = ta.value.slice(end);
        ta.value = before + insert + after;
        const pos = start + insert.length;
        ta.focus();
        ta.setSelectionRange(pos, pos);
      });
    });

    // Writeup templates on create
    const templateSelect = document.getElementById('template');
    const templatesEl = document.getElementById('writeup-templates');
    const contentInput = document.querySelector('.writeup-content-input');
    if (templateSelect && templatesEl && contentInput) {
      let templates = {};
      try { templates = JSON.parse(templatesEl.textContent || '{}'); } catch { /* ignore */ }
      templateSelect.addEventListener('change', () => {
        const key = templateSelect.value;
        if (!key || !templates[key]) return;
        if (contentInput.value.trim() !== '' && !window.confirm('Replace current content with template?')) return;
        contentInput.value = templates[key];
      });
    }

    // TOC mobile toggle
    document.querySelectorAll('.writeup-toc-toggle').forEach((btn) => {
      btn.addEventListener('click', () => {
        const nav = btn.closest('.writeup-toc');
        if (!nav) return;
        const open = nav.classList.toggle('is-open');
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        btn.textContent = open ? 'Hide contents' : 'Show contents';
      });
    });

    // Writeup autosave (edit page)
    const autosaveForm = document.getElementById('autosave-form');
    const autosaveStatus = document.getElementById('autosave-status');
    const titleInput = document.getElementById('title');
    if (autosaveForm && contentInput && titleInput) {
      let timer = null;
      const csrfInput = autosaveForm.querySelector('input[name="_csrf"]');
      const save = async () => {
        if (!csrfInput) return;
        const body = new FormData();
        body.append('_csrf', csrfInput.value);
        body.append('title', titleInput.value);
        body.append('content', contentInput.value);
        body.append('format', 'json');
        try {
          const res = await fetch(autosaveForm.action, {
            method: 'POST',
            body,
            headers: { Accept: 'application/json' },
          });
          if (autosaveStatus) {
            autosaveStatus.textContent = res.status === 204 ? 'Draft saved' : res.ok ? 'Saved' : 'Save failed';
          }
        } catch {
          if (autosaveStatus) autosaveStatus.textContent = 'Save failed';
        }
      };
      const schedule = () => {
        if (timer) clearTimeout(timer);
        timer = setTimeout(save, 10000);
        if (autosaveStatus) autosaveStatus.textContent = 'Unsaved changes…';
      };
      contentInput.addEventListener('input', schedule);
      titleInput.addEventListener('input', schedule);
    }
  });
})();
