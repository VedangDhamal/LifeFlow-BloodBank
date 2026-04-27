<?php /* includes/theme_toggle.php - include this in every page */ ?>
<style>
/* ── LIGHT MODE (default) ─────────────────────────────── */
:root {
  --red:        #c0392b;
  --red2:       #e74c3c;
  --red-light:  #c0392b;
  --dark:       #f8f5f4;
  --dark2:      #ffffff;
  --dark3:      #f0eceb;
  --border:     #e0d5d3;
  --text:       #1a1010;
  --muted:      #6b5555;
  --muted2:     #9e8888;
  --nav-bg:     rgba(255,255,255,0.94);
  --shadow:     rgba(192,57,43,0.08);
}
/* ── DARK MODE ────────────────────────────────────────── */
html.dark {
  --red:        #c0392b;
  --red2:       #e74c3c;
  --red-light:  #ff6b6b;
  --dark:       #0e0c0c;
  --dark2:      #1a1515;
  --dark3:      #241e1e;
  --border:     #2e2525;
  --text:       #f0ebe8;
  --muted:      #8a7d7d;
  --muted2:     #5a4f4f;
  --nav-bg:     rgba(14,12,12,0.88);
  --shadow:     rgba(0,0,0,0.4);
}

/* ── Toggle button ────────────────────────────────────── */
.theme-toggle {
  width: 38px; height: 38px;
  border-radius: 50%;
  border: 1px solid var(--border);
  background: var(--dark3);
  color: var(--text);
  font-size: 17px;
  cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  transition: all 0.2s;
  flex-shrink: 0;
}
.theme-toggle:hover { border-color: var(--red); transform: scale(1.08); }
</style>
<script>
(function() {
  // Apply saved theme instantly before paint
  var saved = localStorage.getItem('lfTheme') || 'light';
  document.documentElement.className = saved;
})();
</script>
