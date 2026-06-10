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
      <p>Encontre rótulos anunciados por outros usuários e reserve a compra com segurança.</p>
    </div>
    <a href="meus_anuncios.php" class="pill-btn pill-primary">Vender um vinho</a>
  </section>

  <section class="container painel-card fade-in-up">
    <?php if ($flash): ?>
      <div class="alerta alerta-<?= escapar($flash['tipo']) ?>"><?= escapar($flash['mensagem']) ?></div>
    <?php endif; ?>

    <?php if (!$anuncios): ?>
      <p class="empty-state">Ainda não há anúncios disponíveis de outros usuários.</p>
    <?php else: ?>
      <div class="market-grid">
        <?php foreach ($anuncios as $anuncio): ?>
          <article class="market-card">
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

            <form method="post" action="controladores/comprar_anuncio.php" class="buy-form">
              <?= campo_csrf() ?>
              <input type="hidden" name="anuncio_id" value="<?= escapar($anuncio['id']) ?>">
              <div class="form-row">
                <div class="form-group">
                  <label for="qtd-<?= escapar($anuncio['id']) ?>">Quantidade</label>
                  <input id="qtd-<?= escapar($anuncio['id']) ?>" name="quantidade" type="number" min="1" max="<?= escapar((string)$anuncio['quantidade']) ?>" value="1" required>
                </div>
              </div>
              <div class="form-group">
                <label for="endereco-<?= escapar($anuncio['id']) ?>">Endereço ou ponto de retirada</label>
                <textarea id="endereco-<?= escapar($anuncio['id']) ?>" name="endereco_entrega" rows="3" maxlength="500" required placeholder="Informe endereço, telefone ou local combinado para entrega."></textarea>
              </div>
              <button type="submit" class="pill-btn pill-primary submit-btn">Reservar compra</button>
            </form>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>
</main>
<?php require __DIR__ . '/visoes/parciais/rodape.php'; ?>
