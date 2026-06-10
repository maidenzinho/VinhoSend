<?php
require_once __DIR__ . '/config/seguranca.php';
require_once __DIR__ . '/repositorios/VinhoRepositorio.php';
require_once __DIR__ . '/repositorios/AnuncioRepositorio.php';
require_once __DIR__ . '/repositorios/CompraRepositorio.php';

exigir_login();

$tituloPagina = 'VinhoSend - Meus Anúncios';
$paginaAtual = 'meus_anuncios';
$flash = obter_flash();
$usuarioId = usuario_atual_id();
$vinhos = (new VinhoRepositorio())->listarPorUsuario($usuarioId);
$repo = new AnuncioRepositorio();
$compraRepo = new CompraRepositorio();
$anuncios = $repo->listarPorVendedor($usuarioId);
$vendas = $compraRepo->listarVendasPorVendedor($usuarioId);
$pendentes = array_values(array_filter($vendas, fn(array $venda): bool => $venda['status'] === 'reservada'));
$enviadas = array_values(array_filter($vendas, fn(array $venda): bool => $venda['status'] === 'enviada'));
$concluidas = array_values(array_filter($vendas, fn(array $venda): bool => $venda['status'] === 'concluida'));
$anuncioEdicao = null;

if (isset($_GET['editar'])) {
    $anuncioEdicao = $repo->buscarDoVendedor((int)$_GET['editar'], $usuarioId);
}

require __DIR__ . '/visoes/parciais/cabecalho.php';
?>
<main class="painel-main">
  <section class="container painel-header fade-in-up">
    <div>
      <p class="eyebrow">Área do vendedor</p>
      <h1>Vender vinhos</h1>
      <p>Publique seus vinhos, acompanhe solicitações de compra e marque os pedidos como enviados.</p>
    </div>
    <a href="marketplace.php" class="pill-btn pill-primary">Ver marketplace</a>
  </section>

  <section class="container seller-summary fade-in-up">
    <article class="seller-stat">
      <span>Anúncios publicados</span>
      <strong><?= count($anuncios) ?></strong>
    </article>
    <article class="seller-stat">
      <span>Solicitações pendentes</span>
      <strong><?= count($pendentes) ?></strong>
    </article>
    <article class="seller-stat">
      <span>Pedidos enviados</span>
      <strong><?= count($enviadas) ?></strong>
    </article>
    <article class="seller-stat">
      <span>Concluídos</span>
      <strong><?= count($concluidas) ?></strong>
    </article>
  </section>

  <?php if ($flash): ?>
    <section class="container">
      <div class="alerta alerta-<?= escapar($flash['tipo']) ?>"><?= escapar($flash['mensagem']) ?></div>
    </section>
  <?php endif; ?>

  <section class="container painel-grid">
    <article class="painel-card fade-in-up">
      <h2><?= $anuncioEdicao ? 'Editar anúncio' : 'Criar anúncio' ?></h2>
      <p class="muted-text">Escolha um vinho já cadastrado e coloque preço, estoque e observações para o comprador.</p>

      <?php if (!$vinhos): ?>
        <p class="empty-state">Cadastre um vinho antes de criar um anúncio.</p>
        <a href="painel.php" class="pill-btn pill-primary">Cadastrar vinho</a>
      <?php else: ?>
        <form method="post" action="controladores/salvar_anuncio.php" class="auth-form">
          <?= campo_csrf() ?>
          <input type="hidden" name="id" value="<?= escapar($anuncioEdicao['id'] ?? '') ?>">
          <div class="form-group">
            <label for="vinho_id">Vinho anunciado</label>
            <select id="vinho_id" name="vinho_id" required>
              <?php foreach ($vinhos as $vinho): ?>
                <option value="<?= escapar($vinho['id']) ?>" <?= isset($anuncioEdicao['vinho_id']) && (int)$anuncioEdicao['vinho_id'] === (int)$vinho['id'] ? 'selected' : '' ?>>
                  <?= escapar($vinho['nome']) ?> - <?= escapar($vinho['tipo']) ?> - <?= escapar((string)$vinho['safra']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="titulo">Título do anúncio</label>
            <input id="titulo" name="titulo" type="text" required maxlength="140" value="<?= escapar($anuncioEdicao['titulo'] ?? '') ?>" placeholder="Ex.: Malbec argentino conservado em adega">
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="preco">Preço</label>
              <input id="preco" name="preco" type="number" min="1" max="999999" step="0.01" required value="<?= escapar($anuncioEdicao['preco'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label for="quantidade">Quantidade</label>
              <input id="quantidade" name="quantidade" type="number" min="1" max="9999" required value="<?= escapar($anuncioEdicao['quantidade'] ?? '1') ?>">
            </div>
          </div>
          <div class="form-group">
            <label for="status">Status</label>
            <?php $statusAtual = $anuncioEdicao['status'] ?? 'ativo'; ?>
            <select id="status" name="status" required>
              <option value="ativo" <?= $statusAtual === 'ativo' ? 'selected' : '' ?>>Ativo</option>
              <option value="pausado" <?= $statusAtual === 'pausado' ? 'selected' : '' ?>>Pausado</option>
            </select>
          </div>
          <div class="form-group">
            <label for="observacoes">Observações para o comprador</label>
            <textarea id="observacoes" name="observacoes" rows="4" maxlength="1000" placeholder="Estado da garrafa, forma de entrega, conservação, detalhes do rótulo..."><?= escapar($anuncioEdicao['observacoes'] ?? '') ?></textarea>
          </div>
          <button type="submit" class="pill-btn pill-primary submit-btn"><?= $anuncioEdicao ? 'Salvar anúncio' : 'Publicar no marketplace' ?></button>
          <?php if ($anuncioEdicao): ?>
            <a href="meus_anuncios.php" class="text-link">Cancelar edição</a>
          <?php endif; ?>
        </form>
      <?php endif; ?>
    </article>

    <article class="painel-card fade-in-up delay-1" id="solicitacoes">
      <h2>Solicitações de compra</h2>
      <p class="muted-text">Quando outro usuário reservar um vinho seu, a solicitação aparece aqui para você separar e enviar.</p>
      <?php if (!$vendas): ?>
        <p class="empty-state">Nenhuma solicitação recebida ainda.</p>
      <?php else: ?>
        <div class="seller-request-list">
          <?php foreach ($vendas as $venda): ?>
            <article class="seller-request-card">
              <div class="request-photo">
                <?php if (!empty($venda['imagem'])): ?>
                  <img src="<?= escapar($venda['imagem']) ?>" alt="Foto de <?= escapar($venda['vinho_nome']) ?>">
                <?php else: ?>
                  <span class="thumb-placeholder"><i class="bi bi-image"></i></span>
                <?php endif; ?>
              </div>
              <div class="request-info">
                <div class="request-title-row">
                  <h3><?= escapar($venda['titulo']) ?></h3>
                  <span class="status-chip status-<?= escapar($venda['status']) ?>"><?= escapar($venda['status_texto']) ?></span>
                </div>
                <p><strong>Comprador:</strong> <?= escapar($venda['comprador_nome']) ?> - <?= escapar($venda['comprador_email'] ?? '') ?></p>
                <p><strong>Vinho:</strong> <?= escapar($venda['vinho_nome']) ?> | <strong>Qtd.:</strong> <?= escapar((string)$venda['quantidade']) ?> | <strong>Total:</strong> R$ <?= number_format((float)$venda['total'], 2, ',', '.') ?></p>
                <p><strong>Entrega combinada:</strong> <?= escapar($venda['endereco_entrega']) ?></p>
                <?php if ($venda['status'] === 'reservada'): ?>
                  <form method="post" action="controladores/enviar_compra.php" onsubmit="return confirm('Marcar esta solicitação como enviada?');">
                    <?= campo_csrf() ?>
                    <input type="hidden" name="compra_id" value="<?= escapar($venda['id']) ?>">
                    <button type="submit" class="pill-btn pill-primary">Marcar como enviada</button>
                  </form>
                <?php elseif ($venda['status'] === 'enviada'): ?>
                  <p class="request-note">Pedido marcado como enviado. Agora aguarde o comprador confirmar o recebimento.</p>
                <?php else: ?>
                  <p class="request-note">Solicitação finalizada.</p>
                <?php endif; ?>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </article>
  </section>

  <section class="container painel-card fade-in-up">
    <h2>Meus vinhos anunciados</h2>
    <p class="muted-text">Aqui ficam os vinhos que você colocou à venda no marketplace.</p>
    <?php if (!$anuncios): ?>
      <p class="empty-state">Você ainda não publicou anúncios.</p>
    <?php else: ?>
      <div class="seller-ad-grid">
        <?php foreach ($anuncios as $anuncio): ?>
          <article class="seller-ad-card">
            <div class="seller-ad-image">
              <?php if (!empty($anuncio['imagem'])): ?>
                <img src="<?= escapar($anuncio['imagem']) ?>" alt="Foto de <?= escapar($anuncio['vinho_nome']) ?>">
              <?php else: ?>
                <div class="market-image placeholder"><i class="bi bi-image"></i><span>Sem foto</span></div>
              <?php endif; ?>
            </div>
            <div class="seller-ad-body">
              <div class="market-card-top">
                <span class="tag-vinho"><?= escapar($anuncio['tipo']) ?> - <?= escapar($anuncio['safra']) ?></span>
                <span class="status-chip status-<?= escapar($anuncio['status']) ?>"><?= escapar($anuncio['status']) ?></span>
              </div>
              <h3><?= escapar($anuncio['titulo']) ?></h3>
              <p class="market-meta"><?= escapar($anuncio['vinho_nome']) ?> - <?= escapar($anuncio['pais']) ?></p>
              <p><?= escapar($anuncio['observacoes'] ?: 'Sem observações adicionais.') ?></p>
              <p><strong>Preço:</strong> R$ <?= number_format((float)$anuncio['preco'], 2, ',', '.') ?> | <strong>Estoque:</strong> <?= escapar((string)$anuncio['quantidade']) ?></p>
              <div class="seller-ad-actions">
                <a class="pill-btn" href="meus_anuncios.php?editar=<?= escapar($anuncio['id']) ?>">Editar</a>
                <?php if ($anuncio['status'] === 'ativo'): ?>
                  <form method="post" action="controladores/pausar_anuncio.php" onsubmit="return confirm('Deseja pausar este anúncio?');">
                    <?= campo_csrf() ?>
                    <input type="hidden" name="id" value="<?= escapar($anuncio['id']) ?>">
                    <button type="submit" class="pill-btn pill-danger">Pausar</button>
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
