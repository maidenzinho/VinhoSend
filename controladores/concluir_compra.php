<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/seguranca.php';
require_once __DIR__ . '/../repositorios/CompraRepositorio.php';
require_once __DIR__ . '/../repositorios/AuditoriaRepositorio.php';
require_once __DIR__ . '/../servicos/Validador.php';

exigir_login();
validar_csrf();

try {
    $compradorId = usuario_atual_id();
    $compraId = Validador::inteiro($_POST['compra_id'] ?? '', 1, 999999, 'Compra');

    $ok = (new CompraRepositorio())->concluirPeloComprador($compraId, $compradorId);
    if (!$ok) {
        throw new RuntimeException('Compra não encontrada ou ainda não enviada.');
    }

    (new AuditoriaRepositorio())->registrar($compradorId, 'CONCLUIR_COMPRA', 'Comprador confirmou recebimento da compra #' . $compraId);
    redirecionar_com_mensagem('../minhas_compras.php', 'sucesso', 'Compra concluída. Obrigado por confirmar o recebimento.');
} catch (Throwable $e) {
    redirecionar_com_mensagem('../minhas_compras.php', 'erro', $e->getMessage());
}
