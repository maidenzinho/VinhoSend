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
              <th>Anúncio</th>
              <th>Vendedor</th>
              <th>Qtd.</th>
              <th>Preço unit.</th>
              <th>Total</th>
              <th>Status</th>
              <th>Entrega</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($compras as $compra): ?>
              <tr>
                <td><?= escapar($compra['titulo']) ?></td>
                <td><?= escapar($compra['vendedor_nome']) ?></td>
                <td><?= escapar((string)$compra['quantidade']) ?></td>
                <td>R$ <?= number_format((float)$compra['preco_unitario'], 2, ',', '.') ?></td>
                <td>R$ <?= number_format((float)$compra['total'], 2, ',', '.') ?></td>
                <td><span class="status-chip status-<?= escapar($compra['status']) ?>"><?= escapar($compra['status']) ?></span></td>
                <td><?= escapar($compra['endereco_entrega']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </section>
</main>
<?php require __DIR__ . '/visoes/parciais/rodape.php'; ?>
