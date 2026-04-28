<?php
require_once __DIR__ . '/config/seguranca.php';
$tituloPagina = 'VinhoSend — Entrar';
$paginaAtual = 'login';
$flash = obter_flash();
require __DIR__ . '/visoes/parciais/cabecalho.php';
?>
<main class="auth-main">
  <section class="auth-card fade-in-up">
    <h1>Bem-vindo de volta!</h1>
    <p>Entre na sua conta e continue compartilhando seus vinhos.</p>
    <?php if ($flash): ?>
      <div class="alerta alerta-<?= escapar($flash['tipo']) ?>"><?= escapar($flash['mensagem']) ?></div>
    <?php endif; ?>
    <form method="post" action="controladores/entrar.php" class="auth-form">
      <?= campo_csrf() ?>
      <div class="form-group">
        <label for="email">E-mail</label>
        <input id="email" name="email" type="email" placeholder="seu@email.com" autocomplete="email" required>
      </div>
      <div class="form-group">
        <label for="senha">Senha</label>
        <div class="password-wrap">
          <input id="senha" name="senha" type="password" placeholder="••••••••" autocomplete="current-password" required>
          <button type="button" id="togglePassword" class="password-toggle" aria-label="Mostrar ou ocultar senha"><i class="bi bi-eye"></i></button>
        </div>
      </div>
      <button type="submit" class="pill-btn pill-primary submit-btn">Entrar</button>
    </form>
    <p class="auth-switch">Não tem uma conta? <a href="registro.php" class="text-link">Criar Conta</a></p>
  </section>
</main>
<?php require __DIR__ . '/visoes/parciais/rodape.php'; ?>
