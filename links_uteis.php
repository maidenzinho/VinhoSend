<?php
require_once __DIR__ . '/config/seguranca.php';

exigir_login();

$tituloPagina = 'VinhoSend - Links Úteis';
$paginaAtual = 'links_uteis';
require __DIR__ . '/visoes/parciais/cabecalho.php';
?>
<main class="painel-main">
  <section class="container painel-header fade-in-up">
    <div>
      <p class="eyebrow">Ajuda rápida</p>
      <h1>Links úteis</h1>
      <p>Informações simples para comprar, vender e combinar entregas com mais segurança.</p>
    </div>
    <a href="marketplace.php" class="pill-btn pill-primary">Ir ao marketplace</a>
  </section>

  <section class="container useful-grid fade-in-up">
    <article class="useful-card">
      <i class="bi bi-cart-check"></i>
      <h2>Como comprar</h2>
      <p>Escolha um vinho no marketplace, informe a quantidade, selecione a forma de pagamento e coloque o endereço no comentário da encomenda.</p>
    </article>
    <article class="useful-card">
      <i class="bi bi-box-seam"></i>
      <h2>Como vender</h2>
      <p>Cadastre o vinho em Meus Vinhos, publique o anúncio em Vender e acompanhe as solicitações recebidas na mesma página.</p>
    </article>
    <article class="useful-card">
      <i class="bi bi-truck"></i>
      <h2>Entrega</h2>
      <p>O vendedor marca o pedido como enviado depois de separar o vinho. O comprador confirma o recebimento em Minhas Compras.</p>
    </article>
    <article class="useful-card">
      <i class="bi bi-receipt"></i>
      <h2>Nota fiscal</h2>
      <p>Ao encomendar, o sistema registra o valor, a forma de pagamento e o comentário com endereço para montar a nota fiscal da compra.</p>
    </article>
    <article class="useful-card">
      <i class="bi bi-shield-check"></i>
      <h2>Segurança</h2>
      <p>As ações precisam de login, os formulários usam token CSRF e os dados sensíveis da entrega ficam protegidos no banco.</p>
    </article>
    <article class="useful-card">
      <i class="bi bi-question-circle"></i>
      <h2>Dúvidas comuns</h2>
      <p>Use a página de contato para dúvidas sobre anúncios, entrega, pagamento combinado ou funcionamento da reserva.</p>
    </article>
  </section>
</main>
<?php require __DIR__ . '/visoes/parciais/rodape.php'; ?>
