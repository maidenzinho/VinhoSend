<?php
require_once __DIR__ . '/config/seguranca.php';
require_once __DIR__ . '/repositorios/CompraRepositorio.php';

exigir_login();

$tituloPagina = 'VinhoSend — Minhas Compras';
$paginaAtual = 'minhas_compras';
$flash = obter_flash();
$compras = (new CompraRepositorio())->listarPorComprador(usuario_atual_id());

require __DIR__ . '/visoes/parciais/cabecalho.php';
?>
<main class="painel-main">
  <section class="container painel-header fade-in-up">
    <div>
      <p class="eyebrow">Histórico do comprador</p>
      <h1>Minhas compras</h1>
      <p>Acompanhe suas encomendas, forma de pagamento e nota fiscal emitida.</p>
    </div>
    <a href="marketplace.php" class="pill-btn pill-primary">Comprar vinhos</a>
  </section>

  <section class="container painel-card fade-in-up">
    <?php if ($flash): ?>
      <div class="alerta alerta-<?= escapar($flash['tipo']) ?>"><?= escapar($flash['mensagem']) ?></div>
    <?php endif; ?>

    <?php if (!$compras): ?>
      <p class="empty-state">Você ainda não reservou compras.</p>
    <?php else: ?>
      <div class="purchase-cards">
        <?php foreach ($compras as $compra): ?>
          <article class="purchase-card">
            <div class="purchase-photo">
              <?php if (!empty($compra['imagem'])): ?>
                <img src="<?= escapar($compra['imagem']) ?>" alt="Foto de <?= escapar($compra['vinho_nome']) ?>">
              <?php else: ?>
                <span class="thumb-placeholder"><i class="bi bi-image"></i></span>
              <?php endif; ?>
            </div>
            <div class="purchase-info">
              <div class="request-title-row">
                <h2><?= escapar($compra['titulo']) ?></h2>
                <span class="status-chip status-<?= escapar($compra['status']) ?>"><?= escapar($compra['status_texto'] ?? $compra['status']) ?></span>
              </div>
              <p><strong>Vendedor:</strong> <?= escapar($compra['vendedor_nome']) ?></p>
              <p><strong>Qtd.:</strong> <?= escapar((string)$compra['quantidade']) ?> | <strong>Total:</strong> R$ <?= number_format((float)$compra['total'], 2, ',', '.') ?></p>
              <p><strong>Pagamento:</strong> <?= escapar($compra['forma_pagamento']) ?></p>
              <p><strong>Comentário / entrega:</strong> <?= escapar($compra['endereco_entrega']) ?></p>
              <p><strong>Nota fiscal:</strong> <?= escapar($compra['numero_nota']) ?></p>
              <div class="seller-ad-actions">
                <a class="pill-btn" href="nota_fiscal.php?id=<?= escapar($compra['id']) ?>"><i class="bi bi-receipt"></i> Ver nota</a>
                <?php if ($compra['status'] === 'enviada'): ?>
                  <form method="post" action="controladores/concluir_compra.php" onsubmit="return confirm('Confirmar que você recebeu este vinho?');">
                    <?= campo_csrf() ?>
                    <input type="hidden" name="compra_id" value="<?= escapar($compra['id']) ?>">
                    <button type="submit" class="pill-btn pill-primary">Confirmar recebimento</button>
                  </form>
                <?php endif; ?>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>
</main>
<?php require __DIR__ . '/visoes/parciais/rodape.php'; ?>
