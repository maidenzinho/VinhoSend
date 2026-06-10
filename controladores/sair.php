<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/seguranca.php';
require_once __DIR__ . '/../repositorios/AuditoriaRepositorio.php';

iniciar_sessao_segura();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirecionar_com_mensagem('../login.php', 'erro', 'Use o botão Sair do sistema para encerrar a sessão.');
}

// Se a sessão estiver velha ou inválida, o logout apenas limpa os dados e volta ao login.
$tokenEnviado = $_POST['csrf_token'] ?? '';
$tokenSessao = $_SESSION[CSRF_NOME] ?? '';
if (!is_string($tokenEnviado) || !is_string($tokenSessao) || $tokenSessao === '' || !hash_equals($tokenSessao, $tokenEnviado)) {
    limpar_sessao_local();
    header('Location: ../login.php');
    exit;
}

$usuarioId = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : null;
(new AuditoriaRepositorio())->registrar($usuarioId, 'LOGOUT', 'Usuário encerrou a sessão.');

limpar_sessao_local();
header('Location: ../login.php');
exit;
