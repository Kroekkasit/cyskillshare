<?php
/** Inline theme bootstrap — prevents flash of wrong theme before CSS/JS load. */
?>
<script>
(() => {
  try {
    const key = 'cyskillshare-theme';
    const saved = localStorage.getItem(key);
    const theme = (saved === 'light' || saved === 'dark')
      ? saved
      : (window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark');
    document.documentElement.setAttribute('data-theme', theme);
  } catch (e) {
    document.documentElement.setAttribute('data-theme', 'dark');
  }
})();
</script>
