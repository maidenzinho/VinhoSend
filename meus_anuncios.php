<?php
require_once __DIR__ . '/config/seguranca.php';
require_once __DIR__ . '/repositorios/VinhoRepositorio.php';
require_once __DIR__ . '/repositorios/AnuncioRepositorio.php';
require_once __DIR__ . '/repositorios/CompraRepositorio.php';

exigir_login();

$tituloPagina = 'VinhoSend — Meus Anúncios';
$paginaAtual = 'meus_anuncios';
$flash = obter_flash();
$usuarioId = usuario_atual_id();
$vinhos = (new VinhoRepositorio())->listarPorUsuario($usuarioId);
$repo = new AnuncioRepositorio();
$anuncios = $repo->listarPorVendedor($usuarioId);
$vendas = (new CompraRepositorio())->listarVendasPorVendedor($usuarioId);
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
      <h1>Meus anúncios</h1>
      <p>Publique seus vinhos no marketplace, controle estoque e acompanhe reservas recebidas.</p>
    </div>
    <a href="marketplace.php" class="pill-btn pill-primary">Ver marketplace</a>
  </section>

  <section class="container painel-grid">
    <article class="painel-card fade-in-up">
      <h2><?= $anuncioEdicao ? 'Editar anúncio' : 'Criar anúncio' ?></h2>
      <?php if ($flash): ?>
        <div class="alerta alerta-<?= escapar($flash['tipo']) ?>"><?= escapar($flash['mensagem']) ?></div>
      <?php endif; ?>

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
                  <?= escapar($vinho['nome']) ?> · <?= escapar($vinho['tipo']) ?> · <?= escapar((string)$vinho['safra']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="titulo">Título do anúncio</label>
            <input id="titulo" name="titulo" type="text" required maxlength="140" value="<?= escapar($anuncioEdicao['titulo'] ?? '') ?>" placeholder="Ex.: Malbec argentino para presente">
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

    <article class="painel-card fade-in-up delay-1">
      <h2>Anúncios publicados</h2>
      <?php if (!$anuncios): ?>
        <p class="empty-state">Você ainda não publicou anúncios.</p>
      <?php else: ?>
        <div class="table-wrap">
          <table class="tabela-vinhos">
            <thead><tr><th>Foto</th><th>Título</th><th>Vinho</th><th>Preço</th><th>Qtd.</th><th>Status</th><th>Ações</th></tr></thead>
            <tbody>
              <?php foreach ($anuncios as $anuncio): ?>
                <tr>
                  <td>
                    <?php if (!empty($anuncio['imagem'])): ?>
                      <img src="<?= escapar($anuncio['imagem']) ?>" alt="Foto de <?= escapar($anuncio['vinho_nome']) ?>" class="thumb-vinho">
                    <?php else: ?>
                      <span class="thumb-placeholder"><i class="bi bi-image"></i></span>
                    <?php endif; ?>
                  </td>
                  <td><?= escapar($anuncio['titulo']) ?></td>
                  <td><?= escapar($anuncio['vinho_nome']) ?></td>
                  <td>R$ <?= number_format((float)$anuncio['preco'], 2, ',', '.') ?></td>
                  <td><?= escapar((string)$anuncio['quantidade']) ?></td>
                  <td><span class="status-chip status-<?= escapar($anuncio['status']) ?>"><?= escapar($anuncio['status']) ?></span></td>
                  <td class="acoes">
                    <a class="table-link" href="meus_anuncios.php?editar=<?= escapar($anuncio['id']) ?>">Editar</a>
                    <?php if ($anuncio['status'] === 'ativo'): ?>
                      <form method="post" action="controladores/pausar_anuncio.php" onsubmit="return confirm('Deseja pausar este anúncio?');">
                        <?= campo_csrf() ?>
                        <input type="hidden" name="id" value="<?= escapar($anuncio['id']) ?>">
                        <button type="submit" class="table-danger">Pausar</button>
                      </form>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </article>
  </section>

  <section class="container painel-card fade-in-up">
    <h2>Reservas recebidas</h2>
    <?php if (!$vendas): ?>
      <p class="empty-state">Nenhuma reserva recebida ainda.</p>
    <?php else: ?>
      <div class="table-wrap">
        <table class="tabela-vinhos">
          <thead><tr><th>Foto</th><th>Comprador</th><th>Anúncio</th><th>Qtd.</th><th>Total</th><th>Status</th><th>Entrega</th></tr></thead>
          <tbody>
            <?php foreach ($vendas as $venda): ?>
              <tr>
                <td>
                  <?php if (!empty($venda['imagem'])): ?>
                    <img src="<?= escapar($venda['imagem']) ?>" alt="Foto de <?= escapar($venda['vinho_nome']) ?>" class="thumb-vinho">
                  <?php else: ?>
                    <span class="thumb-placeholder"><i class="bi bi-image"></i></span>
                  <?php endif; ?>
                </td>
                <td><?= escapar($venda['comprador_nome']) ?></td>
                <td><?= escapar($venda['titulo']) ?></td>
                <td><?= escapar((string)$venda['quantidade']) ?></td>
                <td>R$ <?= number_format((float)$venda['total'], 2, ',', '.') ?></td>
                <td><?= escapar($venda['status']) ?></td>
                <td><?= escapar($venda['endereco_entrega']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </section>
</main>
<?php require __DIR__ . '/visoes/parciais/rodape.php'; ?>
