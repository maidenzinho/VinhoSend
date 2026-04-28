<?php
require_once __DIR__ . '/config/seguranca.php';
$tituloPagina = 'VinhoSend — Criar Conta';
$paginaAtual = 'registro';
$flash = obter_flash();
require __DIR__ . '/visoes/parciais/cabecalho.php';
?>
<main class="auth-main">
  <section class="auth-card fade-in-up">
    <h1>Crie sua conta</h1>
    <p>Junte-se à comunidade VinhoSend com segurança.</p>
    <?php if ($flash): ?>
      <div class="alerta alerta-<?= escapar($flash['tipo']) ?>"><?= escapar($flash['mensagem']) ?></div>
    <?php endif; ?>
    <form method="post" action="controladores/cadastrar_usuario.php" class="auth-form">
      <?= campo_csrf() ?>
      <div class="form-group">
        <label for="nome">Nome completo</label>
        <input id="nome" name="nome" type="text" placeholder="Seu nome" autocomplete="name" required minlength="3" maxlength="120">
      </div>
      <div class="form-group">
        <label for="email">E-mail</label>
        <input id="email" name="email" type="email" placeholder="seu@email.com" autocomplete="email" required maxlength="180">
      </div>
      <div class="form-group">
        <label for="senha">Senha</label>
        <div class="password-wrap">
          <input id="senha" name="senha" type="password" placeholder="Mínimo 8 caracteres" autocomplete="new-password" required minlength="8">
          <button type="button" id="togglePassword" class="password-toggle" aria-label="Mostrar ou ocultar senha"><i class="bi bi-eye"></i></button>
        </div>
      </div>
      <div class="form-group">
        <label for="confirmar_senha">Confirmar senha</label>
        <input id="confirmar_senha" name="confirmar_senha" type="password" placeholder="Repita a senha" autocomplete="new-password" required minlength="8">
      </div>
      <button type="submit" class="pill-btn pill-primary submit-btn">Criar Conta</button>
    </form>
    <p class="auth-switch">Já tem uma conta? <a href="login.php" class="text-link">Entrar</a></p>
  </section>
</main>
<?php require __DIR__ . '/visoes/parciais/rodape.php'; ?>
