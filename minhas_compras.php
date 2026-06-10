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
      <p>Acompanhe as reservas feitas no marketplace e os valores negociados.</p>
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
      <div class="table-wrap">
        <table class="tabela-vinhos">
          <thead>
            <tr>
              <th>Foto</th>
              <th>Anúncio</th>
              <th>Vendedor</th>
              <th>Qtd.</th>
              <th>Preço unit.</th>
              <th>Total</th>
              <th>Status</th>
              <th>Entrega</th>
              <th>Ação</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($compras as $compra): ?>
              <tr>
                <td>
                  <?php if (!empty($compra['imagem'])): ?>
                    <img src="<?= escapar($compra['imagem']) ?>" alt="Foto de <?= escapar($compra['vinho_nome']) ?>" class="thumb-vinho">
                  <?php else: ?>
                    <span class="thumb-placeholder"><i class="bi bi-image"></i></span>
                  <?php endif; ?>
                </td>
                <td><?= escapar($compra['titulo']) ?></td>
                <td><?= escapar($compra['vendedor_nome']) ?></td>
                <td><?= escapar((string)$compra['quantidade']) ?></td>
                <td>R$ <?= number_format((float)$compra['preco_unitario'], 2, ',', '.') ?></td>
                <td>R$ <?= number_format((float)$compra['total'], 2, ',', '.') ?></td>
                <td><span class="status-chip status-<?= escapar($compra['status']) ?>"><?= escapar($compra['status_texto'] ?? $compra['status']) ?></span></td>
                <td><?= escapar($compra['endereco_entrega']) ?></td>
                <td>
                  <?php if ($compra['status'] === 'enviada'): ?>
                    <form method="post" action="controladores/concluir_compra.php" onsubmit="return confirm('Confirmar que você recebeu este vinho?');">
                      <?= campo_csrf() ?>
                      <input type="hidden" name="compra_id" value="<?= escapar($compra['id']) ?>">
                      <button type="submit" class="table-link">Confirmar recebimento</button>
                    </form>
                  <?php else: ?>
                    <span class="muted-text">-</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </section>
</main>
<?php require __DIR__ . '/visoes/parciais/rodape.php'; ?>
