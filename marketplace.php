<?php
require_once __DIR__ . '/config/seguranca.php';
require_once __DIR__ . '/repositorios/AnuncioRepositorio.php';

exigir_login();

$tituloPagina = 'VinhoSend — Marketplace';
$paginaAtual = 'marketplace';
$flash = obter_flash();
$anuncios = (new AnuncioRepositorio())->listarMarketplace(usuario_atual_id());

require __DIR__ . '/visoes/parciais/cabecalho.php';
?>
<main class="painel-main">
  <section class="container painel-header fade-in-up">
    <div>
      <p class="eyebrow">Compra e venda</p>
      <h1>Marketplace de Vinhos</h1>
      <p>Encomende vinhos de outros usuários, informe o pagamento e deixe o endereço no comentário do pedido.</p>
    </div>
    <a href="meus_anuncios.php" class="pill-btn pill-primary">Vender um vinho</a>
  </section>

  <section class="container marketplace-layout fade-in-up">
    <aside class="side-info-card">
      <div class="side-info-title"><i class="bi bi-link-45deg"></i><h2>Links Úteis</h2></div>
      <p>Conteúdos rápidos para comprar e vender melhor.</p>
      <a href="links_uteis.php" class="side-link"><i class="bi bi-cart-check"></i><span>Como comprar</span><i class="bi bi-chevron-right"></i></a>
      <a href="links_uteis.php" class="side-link"><i class="bi bi-truck"></i><span>Política de entrega</span><i class="bi bi-chevron-right"></i></a>
      <a href="links_uteis.php" class="side-link"><i class="bi bi-question-circle"></i><span>Perguntas frequentes</span><i class="bi bi-chevron-right"></i></a>

      <div class="side-info-title side-contact-title"><i class="bi bi-envelope"></i><h2>Contato</h2></div>
      <p>Precisa tirar dúvidas?</p>
      <a href="contato.php" class="pill-btn pill-primary side-contact-button"><i class="bi bi-send"></i> Abrir contato</a>
    </aside>

    <section class="painel-card marketplace-main-card">
      <?php if ($flash): ?>
        <div class="alerta alerta-<?= escapar($flash['tipo']) ?>"><?= escapar($flash['mensagem']) ?></div>
      <?php endif; ?>

      <div class="section-title-row">
        <div>
          <h2>Vinhos disponíveis</h2>
          <p class="muted-text">Escolha um vinho e preencha os dados da encomenda.</p>
        </div>
      </div>

      <?php if (!$anuncios): ?>
        <p class="empty-state">Ainda não há anúncios disponíveis de outros usuários.</p>
      <?php else: ?>
        <div class="market-grid market-grid-wide">
          <?php foreach ($anuncios as $anuncio): ?>
            <article class="market-card enhanced-market-card">
              <div class="market-image-wrap">
                <?php if (!empty($anuncio['imagem'])): ?>
                  <img src="<?= escapar($anuncio['imagem']) ?>" alt="Foto de <?= escapar($anuncio['vinho_nome']) ?>" class="market-image">
                <?php else: ?>
                  <div class="market-image placeholder"><i class="bi bi-image"></i><span>Sem foto</span></div>
                <?php endif; ?>
              </div>
              <div class="market-card-top">
                <span class="tag-vinho"><?= escapar($anuncio['tipo']) ?> · <?= escapar($anuncio['safra']) ?></span>
                <strong>R$ <?= number_format((float)$anuncio['preco'], 2, ',', '.') ?></strong>
              </div>
              <h2><?= escapar($anuncio['titulo']) ?></h2>
              <p class="market-meta"><?= escapar($anuncio['vinho_nome']) ?> · <?= escapar($anuncio['pais']) ?></p>
              <p><?= escapar($anuncio['observacoes'] ?: 'Vendedor não informou observações adicionais.') ?></p>
              <p class="market-seller">Vendedor: <?= escapar($anuncio['vendedor_nome']) ?> · Estoque: <?= escapar((string)$anuncio['quantidade']) ?></p>

              <form method="post" action="controladores/comprar_anuncio.php" class="buy-form order-form" data-unit-price="<?= escapar((string)$anuncio['preco']) ?>">
                <?= campo_csrf() ?>
                <input type="hidden" name="anuncio_id" value="<?= escapar($anuncio['id']) ?>">
                <div class="form-row">
                  <div class="form-group">
                    <label for="qtd-<?= escapar($anuncio['id']) ?>">Quantidade</label>
                    <input id="qtd-<?= escapar($anuncio['id']) ?>" name="quantidade" type="number" min="1" max="<?= escapar((string)$anuncio['quantidade']) ?>" value="1" required data-quantity-input>
                  </div>
                  <div class="form-group">
                    <label>Valor do pagamento</label>
                    <div class="readonly-money" data-order-total>R$ <?= number_format((float)$anuncio['preco'], 2, ',', '.') ?></div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="pagamento-<?= escapar($anuncio['id']) ?>">Forma de pagamento</label>
                  <select id="pagamento-<?= escapar($anuncio['id']) ?>" name="forma_pagamento" required>
                    <option value="Pix">Pix</option>
                    <option value="Cartão de crédito">Cartão de crédito</option>
                    <option value="Cartão de débito">Cartão de débito</option>
                    <option value="Dinheiro na entrega">Dinheiro na entrega</option>
                    <option value="Transferência">Transferência</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="comentario-<?= escapar($anuncio['id']) ?>">Comentário da encomenda</label>
                  <textarea id="comentario-<?= escapar($anuncio['id']) ?>" name="comentario_entrega" rows="4" maxlength="700" required placeholder="Coloque aqui o endereço de entrega, telefone e observações. Ex.: Rua..., bairro..., CEP..., entregar à tarde."></textarea>
                </div>
                <button type="submit" class="pill-btn pill-primary submit-btn"><i class="bi bi-receipt"></i> Encomendar e emitir nota</button>
              </form>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>
  </section>
</main>
<?php require __DIR__ . '/visoes/parciais/rodape.php'; ?>
