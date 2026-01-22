// Global Theme & Font Management System
(function() {
  const html = document.documentElement;
  
  // Load saved preferences on page load
  function initPreferences() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    const savedFont = localStorage.getItem('font') || 'default';
    applyTheme(savedTheme);
    applyFont(savedFont);
  }
  
  // Apply theme to entire document
  function applyTheme(theme) {
    if (theme === 'dark') {
      html.setAttribute('data-theme', 'dark');
      document.body.classList.add('dark-mode');
    } else {
      html.removeAttribute('data-theme');
      document.body.classList.remove('dark-mode');
    }
    localStorage.setItem('theme', theme);
  }
  
  // Apply font to entire document
  function applyFont(font) {
    html.setAttribute('data-font', font || 'default');
    localStorage.setItem('font', font || 'default');
  }
  
  // Initialize preferences on page load
  initPreferences();
  
  // Make functions globally available
  window.applyTheme = applyTheme;
  window.applyFont = applyFont;
  
  // Listen for storage changes (if preferences changed in another tab)
  window.addEventListener('storage', function(e) {
    if (e.key === 'theme') {
      applyTheme(e.newValue || 'light');
    }
    if (e.key === 'font') {
      applyFont(e.newValue || 'default');
    }
  });
})();
