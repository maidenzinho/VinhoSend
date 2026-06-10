<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/seguranca.php';
require_once __DIR__ . '/../repositorios/AnuncioRepositorio.php';
require_once __DIR__ . '/../repositorios/AuditoriaRepositorio.php';
require_once __DIR__ . '/../servicos/Validador.php';

exigir_login();
validar_csrf();

try {
    $usuarioId = usuario_atual_id();
    $id = Validador::inteiro($_POST['id'] ?? '', 1, 999999, 'ID');
    (new AnuncioRepositorio())->pausar($id, $usuarioId);
    (new AuditoriaRepositorio())->registrar($usuarioId, 'PAUSAR_ANUNCIO', 'Anúncio pausado: #' . $id);
    redirecionar_com_mensagem('../meus_anuncios.php', 'sucesso', 'Anúncio pausado com segurança.');
} catch (Throwable $e) {
    redirecionar_com_mensagem('../meus_anuncios.php', 'erro', $e->getMessage());
}
