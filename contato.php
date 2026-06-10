<?php
require_once __DIR__ . '/config/seguranca.php';
require_once __DIR__ . '/repositorios/AuditoriaRepositorio.php';

exigir_login();

$tituloPagina = 'VinhoSend - Contato';
$paginaAtual = 'contato';
$flash = obter_flash();
require __DIR__ . '/visoes/parciais/cabecalho.php';
?>
<main class="painel-main">
  <section class="container painel-header fade-in-up">
    <div>
      <p class="eyebrow">Atendimento</p>
      <h1>Contato</h1>
      <p>Envie uma mensagem para tirar dúvidas sobre compras, vendas, entrega ou uso do marketplace.</p>
    </div>
  </section>

  <section class="container contact-layout fade-in-up">
    <article class="painel-card">
      <?php if ($flash): ?>
        <div class="alerta alerta-<?= escapar($flash['tipo']) ?>"><?= escapar($flash['mensagem']) ?></div>
      <?php endif; ?>
      <h2>Enviar mensagem</h2>
      <form method="post" action="controladores/enviar_contato.php" class="auth-form">
        <?= campo_csrf() ?>
        <div class="form-group">
          <label for="assunto">Assunto</label>
          <input id="assunto" name="assunto" type="text" maxlength="120" required placeholder="Ex.: Dúvida sobre entrega">
        </div>
        <div class="form-group">
          <label for="mensagem">Mensagem</label>
          <textarea id="mensagem" name="mensagem" rows="7" maxlength="1000" required placeholder="Escreva sua dúvida aqui."></textarea>
        </div>
        <button type="submit" class="pill-btn pill-primary submit-btn"><i class="bi bi-send"></i> Enviar mensagem</button>
      </form>
    </article>

    <aside class="painel-card contact-info">
      <h2>Canais</h2>
      <p><strong>E-mail:</strong> contato@vinhosend.com</p>
      <p><strong>Telefone:</strong> (11) 99999-8888</p>
      <p><strong>Horário:</strong> segunda a sexta, das 9h às 18h</p>
      <p class="muted-text">Nesta versão do projeto, o contato fica registrado na auditoria para demonstração da ação no sistema.</p>
    </aside>
  </section>
</main>
<?php require __DIR__ . '/visoes/parciais/rodape.php'; ?>
