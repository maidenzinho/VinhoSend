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


  document.querySelectorAll('[data-menu-dropdown]').forEach(function (dropdown) {
    const button = dropdown.querySelector('[data-menu-button]');
    const panel = dropdown.querySelector('[data-menu-panel]');
    if (!button || !panel) return;

    button.addEventListener('click', function (event) {
      event.stopPropagation();
      const open = dropdown.classList.toggle('is-open');
      button.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    panel.addEventListener('click', function (event) {
      event.stopPropagation();
    });
  });


  document.querySelectorAll('[data-unit-price]').forEach(function (form) {
    const quantity = form.querySelector('[data-quantity-input]');
    const total = form.querySelector('[data-order-total]');
    const unit = parseFloat(String(form.dataset.unitPrice || '0').replace(',', '.'));
    const updateTotal = function () {
      if (!quantity || !total) return;
      const qtd = Math.max(1, parseInt(quantity.value || '1', 10));
      const value = unit * qtd;
      total.textContent = value.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    };
    if (quantity) quantity.addEventListener('input', updateTotal);
    updateTotal();
  });

  document.addEventListener('click', function () {
    document.querySelectorAll('[data-menu-dropdown].is-open').forEach(function (dropdown) {
      dropdown.classList.remove('is-open');
      const button = dropdown.querySelector('[data-menu-button]');
      if (button) button.setAttribute('aria-expanded', 'false');
    });
  });

})();
