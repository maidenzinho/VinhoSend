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
    $endereco = Validador::texto($_POST['endereco_entrega'] ?? '', 10, 500, 'Endereço de entrega');

    $anuncio = (new AnuncioRepositorio())->buscarDisponivelParaCompra($anuncioId, $compradorId);
    if (!$anuncio) {
        throw new InvalidArgumentException('Anúncio indisponível ou inválido para compra.');
    }

    $compra = new Compra(
        null,
        $anuncioId,
        $compradorId,
        (int)$anuncio['vendedor_id'],
        $quantidade,
        (float)$anuncio['preco'],
        round((float)$anuncio['preco'] * $quantidade, 2),
        $endereco
    );

    (new CompraRepositorio())->reservarCompra($compra);
    (new AuditoriaRepositorio())->registrar($compradorId, 'RESERVAR_COMPRA', 'Compra reservada no anúncio #' . $anuncioId);
    redirecionar_com_mensagem('../minhas_compras.php', 'sucesso', 'Compra reservada com sucesso. Combine pagamento e entrega com o vendedor.');
} catch (Throwable $e) {
    redirecionar_com_mensagem('../marketplace.php', 'erro', $e->getMessage());
}
