<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/seguranca.php';
require_once __DIR__ . '/../repositorios/VinhoRepositorio.php';
require_once __DIR__ . '/../repositorios/AuditoriaRepositorio.php';
require_once __DIR__ . '/../servicos/Validador.php';

exigir_login();
validar_csrf();

try {
    $id = Validador::inteiro($_POST['id'] ?? '', 1, 999999, 'ID');
    $usuarioId = usuario_atual_id();
    $repo = new VinhoRepositorio();
    $vinho = $repo->buscarDoUsuario($id, $usuarioId);
    if (!$vinho) {
        throw new InvalidArgumentException('Vinho não encontrado para este usuário.');
    }
    $repo->excluir($id, $usuarioId);
    (new AuditoriaRepositorio())->registrar($usuarioId, 'EXCLUIR_VINHO', 'Vinho excluído: ' . $vinho['nome']);
    redirecionar_com_mensagem('../painel.php', 'sucesso', 'Vinho excluído com sucesso.');
} catch (Throwable $e) {
    redirecionar_com_mensagem('../painel.php', 'erro', $e->getMessage());
}
