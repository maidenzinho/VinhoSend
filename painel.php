<?php
require_once __DIR__ . '/config/seguranca.php';
require_once __DIR__ . '/repositorios/VinhoRepositorio.php';

exigir_login();

$tituloPagina = 'VinhoSend — Meus Vinhos';
$paginaAtual = 'painel';
$flash = obter_flash();
$repo = new VinhoRepositorio();
$vinhos = $repo->listarPorUsuario(usuario_atual_id());
$vinhoEdicao = null;

if (isset($_GET['editar'])) {
    $vinhoEdicao = $repo->buscarDoUsuario((int)$_GET['editar'], usuario_atual_id());
}

require __DIR__ . '/visoes/parciais/cabecalho.php';
?>
<main class="painel-main">
  <section class="container painel-header fade-in-up">
    <div>
      <p class="eyebrow">Área autenticada</p>
      <h1>Meus Vinhos</h1>
      <p>Olá, <?= escapar(usuario_atual_nome()) ?>. Cadastre, liste, edite e exclua seus rótulos favoritos.</p>
    </div>
    <a href="controladores/sair.php" class="pill-btn pill-primary">Sair da Conta</a>
  </section>

  <section class="container painel-grid">
    <article class="painel-card fade-in-up">
      <h2><?= $vinhoEdicao ? 'Editar vinho' : 'Cadastrar vinho' ?></h2>
      <?php if ($flash): ?>
        <div class="alerta alerta-<?= escapar($flash['tipo']) ?>"><?= escapar($flash['mensagem']) ?></div>
      <?php endif; ?>

      <form method="post" action="controladores/salvar_vinho.php" class="auth-form">
        <?= campo_csrf() ?>
        <input type="hidden" name="id" value="<?= escapar($vinhoEdicao['id'] ?? '') ?>">
        <div class="form-group">
          <label for="nome">Nome do vinho</label>
          <input id="nome" name="nome" type="text" required maxlength="120" value="<?= escapar($vinhoEdicao['nome'] ?? '') ?>">
        </div>
        <div class="form-row">
          <div class="form-group">
            <label for="tipo">Tipo</label>
            <select id="tipo" name="tipo" required>
              <?php
                $tipos = ['Tinto', 'Branco', 'Rosé', 'Espumante', 'Sobremesa'];
                $tipoAtual = $vinhoEdicao['tipo'] ?? '';
                foreach ($tipos as $tipo):
              ?>
                <option value="<?= escapar($tipo) ?>" <?= $tipoAtual === $tipo ? 'selected' : '' ?>><?= escapar($tipo) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="pais">País</label>
            <input id="pais" name="pais" type="text" required maxlength="80" value="<?= escapar($vinhoEdicao['pais'] ?? '') ?>">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label for="safra">Safra</label>
            <input id="safra" name="safra" type="number" min="1900" max="<?= date('Y') ?>" required value="<?= escapar($vinhoEdicao['safra'] ?? date('Y')) ?>">
          </div>
          <div class="form-group">
            <label for="nota">Nota</label>
            <input id="nota" name="nota" type="number" min="0" max="10" step="0.1" required value="<?= escapar($vinhoEdicao['nota'] ?? '8.0') ?>">
          </div>
        </div>
        <div class="form-group">
          <label for="descricao">Descrição</label>
          <textarea id="descricao" name="descricao" rows="4" maxlength="1000"><?= escapar($vinhoEdicao['descricao'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="pill-btn pill-primary submit-btn"><?= $vinhoEdicao ? 'Salvar Alterações' : 'Cadastrar Vinho' ?></button>
        <?php if ($vinhoEdicao): ?>
          <a href="painel.php" class="text-link">Cancelar edição</a>
        <?php endif; ?>
      </form>
    </article>

    <article class="painel-card fade-in-up delay-1">
      <h2>Lista de vinhos</h2>
      <?php if (!$vinhos): ?>
        <p class="empty-state">Nenhum vinho cadastrado ainda.</p>
      <?php else: ?>
        <div class="table-wrap">
          <table class="tabela-vinhos">
            <thead>
              <tr>
                <th>Nome</th>
                <th>Tipo</th>
                <th>País</th>
                <th>Safra</th>
                <th>Nota</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($vinhos as $vinho): ?>
                <tr>
                  <td><?= escapar($vinho['nome']) ?></td>
                  <td><?= escapar($vinho['tipo']) ?></td>
                  <td><?= escapar($vinho['pais']) ?></td>
                  <td><?= escapar($vinho['safra']) ?></td>
                  <td><?= escapar($vinho['nota']) ?></td>
                  <td class="acoes">
                    <a class="table-link" href="painel.php?editar=<?= escapar($vinho['id']) ?>">Editar</a>
                    <form method="post" action="controladores/excluir_vinho.php" onsubmit="return confirm('Deseja excluir este vinho?');">
                      <?= campo_csrf() ?>
                      <input type="hidden" name="id" value="<?= escapar($vinho['id']) ?>">
                      <button type="submit" class="table-danger">Excluir</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </article>
  </section>
</main>
<?php require __DIR__ . '/visoes/parciais/rodape.php'; ?>
