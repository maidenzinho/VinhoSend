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

  const passwordInput = document.getElementById('password');
  const togglePassword = document.getElementById('togglePassword');
  const confirmInput = document.getElementById('confirmPassword');

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

  const authForm = document.getElementById('authForm');
  if (authForm) {
    authForm.addEventListener('submit', function (event) {
      event.preventDefault();

      if (confirmInput && passwordInput && confirmInput.value !== passwordInput.value) {
        alert('As senhas não coincidem.');
        return;
      }

      const isRegisterPage = document.body.dataset.page === 'registro';
      alert(isRegisterPage ? 'Conta criada com sucesso!' : 'Login realizado!');
    });
  }
})();
