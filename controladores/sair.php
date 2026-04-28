<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/sessao.php';
require_once __DIR__ . '/../repositorios/AuditoriaRepositorio.php';

iniciar_sessao_segura();
$usuarioId = $_SESSION['usuario_id'] ?? null;
if ($usuarioId) {
    (new AuditoriaRepositorio())->registrar((int)$usuarioId, 'LOGOUT', 'Usuário encerrou a sessão.');
}
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}
session_destroy();
header('Location: ../index.html');
exit;
