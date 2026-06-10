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
  <meta name="description" content="VinhoSend — marketplace para cadastro, compra e venda de vinhos.">
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
      <a href="<?= usuario_logado() ? 'painel.php' : 'index.html' ?>" class="brand" aria-label="VinhoSend">
        <img src="assets/logo-taca-branca.svg" alt="Logo VinhoSend" class="brand-logo">
        <span class="brand-block">
          <span class="brand-text">VinhoSend</span>
          <span class="brand-subtitle">Marketplace de Vinhos</span>
        </span>
      </a>

      <div class="nav-actions nav-actions-dropdown">
        <?php if (usuario_logado()): ?>
          <div class="menu-dropdown" data-menu-dropdown>
            <button type="button" class="pill-btn pill-secondary menu-button" data-menu-button aria-expanded="false" aria-haspopup="true">
              Menu <i class="bi bi-chevron-down"></i>
            </button>
            <div class="menu-panel" data-menu-panel>
              <a href="painel.php" class="menu-item <?= $paginaAtual === 'painel' ? 'is-active' : '' ?>"><i class="bi bi-bottle"></i> Meus Vinhos</a>
              <a href="marketplace.php" class="menu-item <?= $paginaAtual === 'marketplace' ? 'is-active' : '' ?>"><i class="bi bi-shop"></i> Marketplace</a>
              <a href="meus_anuncios.php" class="menu-item <?= $paginaAtual === 'meus_anuncios' ? 'is-active' : '' ?>"><i class="bi bi-tags"></i> Vender</a>
              <a href="minhas_compras.php" class="menu-item <?= $paginaAtual === 'minhas_compras' ? 'is-active' : '' ?>"><i class="bi bi-bag-check"></i> Compras</a>
              <a href="links_uteis.php" class="menu-item <?= $paginaAtual === 'links_uteis' ? 'is-active' : '' ?>"><i class="bi bi-link-45deg"></i> Links Úteis</a>
              <a href="contato.php" class="menu-item <?= $paginaAtual === 'contato' ? 'is-active' : '' ?>"><i class="bi bi-envelope"></i> Contato</a>
              <form method="post" action="controladores/sair.php" class="menu-logout-form">
                <?= campo_csrf() ?>
                <button type="submit" class="menu-item menu-logout"><i class="bi bi-box-arrow-right"></i> Sair</button>
              </form>
            </div>
          </div>
        <?php else: ?>
          <div class="menu-dropdown" data-menu-dropdown>
            <button type="button" class="pill-btn pill-secondary menu-button" data-menu-button aria-expanded="false" aria-haspopup="true">
              Menu <i class="bi bi-chevron-down"></i>
            </button>
            <div class="menu-panel" data-menu-panel>
              <a href="login.php" class="menu-item <?= $paginaAtual === 'login' ? 'is-active' : '' ?>"><i class="bi bi-box-arrow-in-right"></i> Entrar</a>
              <a href="registro.php" class="menu-item <?= $paginaAtual === 'registro' ? 'is-active' : '' ?>"><i class="bi bi-person-plus"></i> Criar Conta</a>
            </div>
          </div>
        <?php endif; ?>
        <button type="button" id="themeToggle" class="theme-toggle" aria-label="Alternar tema">
          <i class="bi bi-moon-stars-fill icon-moon"></i>
          <i class="bi bi-sun-fill icon-sun"></i>
        </button>
      </div>
    </nav>
