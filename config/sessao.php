<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function iniciar_sessao_segura(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

    session_name(SESSAO_NOME);
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $https,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();

    if (empty($_SESSION['criada_em'])) {
        $_SESSION['criada_em'] = time();
        session_regenerate_id(true);
    }
}

function usuario_existe_no_banco(int $usuarioId): bool
{
    if ($usuarioId <= 0) {
        return false;
    }

    try {
        require_once __DIR__ . '/Conexao.php';
        $stmt = Conexao::obter()->prepare('SELECT id FROM usuarios WHERE id = ? LIMIT 1');
        $stmt->execute([$usuarioId]);
        return (bool)$stmt->fetchColumn();
    } catch (Throwable $e) {
        return false;
    }
}

function limpar_sessao_local(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
}

function usuario_logado(): bool
{
    iniciar_sessao_segura();

    $usuarioId = (int)($_SESSION['usuario_id'] ?? 0);
    if ($usuarioId <= 0) {
        return false;
    }

    // Garante que uma sessão antiga não continue válida se o usuário foi apagado do banco.
    if (!usuario_existe_no_banco($usuarioId)) {
        limpar_sessao_local();
        return false;
    }

    return true;
}

function caminho_login(): string
{
    $script = (string)($_SERVER['SCRIPT_NAME'] ?? '');
    return str_contains($script, '/controladores/') ? '../login.php' : 'login.php';
}

function exigir_login(): void
{
    if (!usuario_logado()) {
        iniciar_sessao_segura();
        $_SESSION['flash'] = ['tipo' => 'erro', 'mensagem' => 'Entre na sua conta para acessar esta área.'];
        header('Location: ' . caminho_login());
        exit;
    }
}

function usuario_atual_id(): int
{
    iniciar_sessao_segura();
    return (int)($_SESSION['usuario_id'] ?? 0);
}

function usuario_atual_nome(): string
{
    iniciar_sessao_segura();
    return (string)($_SESSION['usuario_nome'] ?? 'Usuário');
}
