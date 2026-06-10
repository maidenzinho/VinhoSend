<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/seguranca.php';
require_once __DIR__ . '/../repositorios/CompraRepositorio.php';
require_once __DIR__ . '/../repositorios/AuditoriaRepositorio.php';
require_once __DIR__ . '/../servicos/Validador.php';

exigir_login();
validar_csrf();

try {
    $vendedorId = usuario_atual_id();
    $compraId = Validador::inteiro($_POST['compra_id'] ?? '', 1, 999999, 'Compra');

    $ok = (new CompraRepositorio())->marcarComoEnviada($compraId, $vendedorId);
    if (!$ok) {
        throw new RuntimeException('Solicitação não encontrada ou já enviada.');
    }

    (new AuditoriaRepositorio())->registrar($vendedorId, 'ENVIAR_COMPRA', 'Vendedor marcou a compra #' . $compraId . ' como enviada');
    redirecionar_com_mensagem('../meus_anuncios.php#solicitacoes', 'sucesso', 'Solicitação marcada como enviada. O comprador já pode acompanhar em Minhas Compras.');
} catch (Throwable $e) {
    redirecionar_com_mensagem('../meus_anuncios.php#solicitacoes', 'erro', $e->getMessage());
}
