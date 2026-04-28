<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config/seguranca.php';
$tituloPagina = $tituloPagina ?? 'VinhoSend';
$paginaAtual = $paginaAtual ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= escapar($tituloPagina) ?></title>
  <meta name="description" content="VinhoSend — conectando pessoas, compartilhando histórias.">
  <meta name="author" content="VinhoSend">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="styles.css">
</head>
<body data-page="<?= escapar($paginaAtual) ?>">
  <div class="site-shell">
    <nav class="navbar">
      <a href="index.html" class="brand" aria-label="VinhoSend">
        <img src="assets/logo-taca-branca.svg" alt="Logo VinhoSend" class="brand-logo">
        <span class="brand-text">VinhoSend</span>
      </a>
      <div class="nav-actions">
        <?php if (usuario_logado()): ?>
          <a href="painel.php" class="pill-btn pill-secondary <?= $paginaAtual === 'painel' ? 'is-active' : '' ?>">Meus Vinhos</a>
          <a href="controladores/sair.php" class="pill-btn pill-secondary">Sair</a>
        <?php else: ?>
          <a href="login.php" class="pill-btn pill-secondary <?= $paginaAtual === 'login' ? 'is-active' : '' ?>">Entrar</a>
          <a href="registro.php" class="pill-btn pill-secondary <?= $paginaAtual === 'registro' ? 'is-active' : '' ?>">Criar Conta</a>
        <?php endif; ?>
        <button type="button" id="themeToggle" class="theme-toggle" aria-label="Alternar tema">
          <i class="bi bi-moon-stars-fill icon-moon"></i>
          <i class="bi bi-sun-fill icon-sun"></i>
        </button>
      </div>
    </nav>
