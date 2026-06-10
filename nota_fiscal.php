<?php
require_once __DIR__ . '/config/seguranca.php';
require_once __DIR__ . '/repositorios/CompraRepositorio.php';
require_once __DIR__ . '/servicos/Validador.php';

exigir_login();

$tituloPagina = 'VinhoSend - Nota Fiscal';
$paginaAtual = 'minhas_compras';
$compraId = Validador::inteiro($_GET['id'] ?? '', 1, 999999, 'Nota fiscal');
$nota = (new CompraRepositorio())->buscarNotaFiscal($compraId, usuario_atual_id());

if (!$nota) {
    redirecionar_com_mensagem('minhas_compras.php', 'erro', 'Nota fiscal não encontrada.');
}

require __DIR__ . '/visoes/parciais/cabecalho.php';
?>
<main class="painel-main">
  <section class="container painel-header fade-in-up no-print">
    <div>
      <p class="eyebrow">Documento da compra</p>
      <h1>Nota fiscal</h1>
      <p>Resumo da encomenda com valor, pagamento e endereço informado no comentário.</p>
    </div>
    <button type="button" onclick="window.print()" class="pill-btn pill-primary"><i class="bi bi-printer"></i> Imprimir / salvar PDF</button>
  </section>

  <section class="container invoice-page fade-in-up">
    <article class="invoice-card">
      <div class="invoice-top">
        <div>
          <h2>Nota Fiscal</h2>
          <p>VinhoSend Marketplace de Vinhos</p>
        </div>
        <span class="status-chip status-concluida">Emitida</span>
      </div>

      <div class="invoice-grid">
        <div>
          <span>Nº da nota</span>
          <strong><?= escapar($nota['numero_nota']) ?></strong>
        </div>
        <div>
          <span>Data de emissão</span>
          <strong><?= escapar(date('d/m/Y H:i', strtotime((string)$nota['nota_emitida_em']))) ?></strong>
        </div>
        <div>
          <span>Cliente</span>
          <strong><?= escapar($nota['comprador_nome']) ?></strong>
          <small><?= escapar($nota['comprador_email']) ?></small>
        </div>
        <div>
          <span>Vendedor</span>
          <strong><?= escapar($nota['vendedor_nome']) ?></strong>
          <small><?= escapar($nota['vendedor_email']) ?></small>
        </div>
      </div>

      <div class="invoice-line"></div>

      <div class="invoice-grid invoice-grid-wide">
        <div>
          <span>Vinho</span>
          <strong><?= escapar($nota['vinho_nome']) ?> <?= escapar((string)$nota['safra']) ?></strong>
          <small><?= escapar($nota['tipo']) ?></small>
        </div>
        <div>
          <span>Quantidade</span>
          <strong><?= escapar((string)$nota['quantidade']) ?></strong>
        </div>
        <div>
          <span>Valor unitário</span>
          <strong>R$ <?= number_format((float)$nota['preco_unitario'], 2, ',', '.') ?></strong>
        </div>
        <div>
          <span>Valor total</span>
          <strong>R$ <?= number_format((float)$nota['total'], 2, ',', '.') ?></strong>
        </div>
      </div>

      <div class="invoice-line"></div>

      <div class="invoice-grid invoice-grid-wide">
        <div>
          <span>Forma de pagamento</span>
          <strong><?= escapar($nota['forma_pagamento']) ?></strong>
        </div>
        <div>
          <span>Status do pedido</span>
          <strong><?= escapar($nota['status_texto']) ?></strong>
        </div>
      </div>

      <div class="invoice-address">
        <span>Comentário com endereço de entrega</span>
        <p><?= nl2br(escapar($nota['endereco_entrega'])) ?></p>
      </div>
    </article>
  </section>
</main>
<?php require __DIR__ . '/visoes/parciais/rodape.php'; ?>
