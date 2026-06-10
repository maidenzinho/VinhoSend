<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/seguranca.php';
require_once __DIR__ . '/../repositorios/AuditoriaRepositorio.php';
require_once __DIR__ . '/../servicos/Validador.php';

exigir_login();
validar_csrf();

try {
    $assunto = Validador::texto($_POST['assunto'] ?? '', 3, 120, 'Assunto');
    $mensagem = Validador::texto($_POST['mensagem'] ?? '', 10, 1000, 'Mensagem');

    (new AuditoriaRepositorio())->registrar(usuario_atual_id(), 'CONTATO', $assunto . ' - ' . substr($mensagem, 0, 120));
    redirecionar_com_mensagem('../contato.php', 'sucesso', 'Mensagem registrada com sucesso.');
} catch (Throwable $e) {
    redirecionar_com_mensagem('../contato.php', 'erro', $e->getMessage());
}
