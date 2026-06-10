<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/seguranca.php';
require_once __DIR__ . '/../repositorios/CompraRepositorio.php';
require_once __DIR__ . '/../repositorios/AnuncioRepositorio.php';
require_once __DIR__ . '/../repositorios/AuditoriaRepositorio.php';
require_once __DIR__ . '/../modelos/Compra.php';
require_once __DIR__ . '/../servicos/Validador.php';

exigir_login();
validar_csrf();

try {
    $compradorId = usuario_atual_id();
    $anuncioId = Validador::inteiro($_POST['anuncio_id'] ?? '', 1, 999999, 'Anúncio');
    $quantidade = Validador::inteiro($_POST['quantidade'] ?? '', 1, 9999, 'Quantidade');
    $comentarioEntrega = Validador::texto($_POST['comentario_entrega'] ?? '', 10, 700, 'Comentário de entrega');
    $formaPagamento = Validador::texto($_POST['forma_pagamento'] ?? '', 3, 40, 'Forma de pagamento');
    $formasPermitidas = ['Pix', 'Cartão de crédito', 'Cartão de débito', 'Dinheiro na entrega', 'Transferência'];

    if (!in_array($formaPagamento, $formasPermitidas, true)) {
        throw new InvalidArgumentException('Forma de pagamento inválida.');
    }

    $anuncio = (new AnuncioRepositorio())->buscarDisponivelParaCompra($anuncioId, $compradorId);
    if (!$anuncio) {
        throw new InvalidArgumentException('Anúncio indisponível ou inválido para compra.');
    }

    $total = round((float)$anuncio['preco'] * $quantidade, 2);

    $compra = new Compra(
        null,
        $anuncioId,
        $compradorId,
        (int)$anuncio['vendedor_id'],
        $quantidade,
        (float)$anuncio['preco'],
        $total,
        $comentarioEntrega,
        $formaPagamento
    );

    $compraId = (new CompraRepositorio())->reservarCompra($compra);
    (new AuditoriaRepositorio())->registrar($compradorId, 'RESERVAR_COMPRA', 'Compra reservada no anúncio #' . $anuncioId . ' com nota fiscal #' . $compraId);
    redirecionar_com_mensagem('../minhas_compras.php', 'sucesso', 'Encomenda registrada e nota fiscal emitida.');
} catch (Throwable $e) {
    redirecionar_com_mensagem('../marketplace.php', 'erro', $e->getMessage());
}
