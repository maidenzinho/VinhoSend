(function () {
  const root = document.documentElement;
  const themeKey = 'vinhosend-theme';
  const savedTheme = localStorage.getItem(themeKey);
  if (savedTheme === 'dark') root.classList.add('dark');

  const themeToggle = document.getElementById('themeToggle');
  if (themeToggle) {
    themeToggle.addEventListener('click', function () {
      root.classList.toggle('dark');
      localStorage.setItem(themeKey, root.classList.contains('dark') ? 'dark' : 'light');
    });
  }

  const passwordInput = document.getElementById('senha');
  const togglePassword = document.getElementById('togglePassword');

  if (passwordInput && togglePassword) {
    const icon = togglePassword.querySelector('i');
    togglePassword.addEventListener('click', function () {
      const showing = passwordInput.type === 'text';
      passwordInput.type = showing ? 'password' : 'text';
      if (icon) {
        icon.className = showing ? 'bi bi-eye' : 'bi bi-eye-slash';
      }
    });
  }
})();
