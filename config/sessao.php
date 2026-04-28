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

function usuario_logado(): bool
{
    iniciar_sessao_segura();
    return isset($_SESSION['usuario_id']);
}

function exigir_login(): void
{
    if (!usuario_logado()) {
        header('Location: login.php');
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
