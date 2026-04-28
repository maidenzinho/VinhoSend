<?php
declare(strict_types=1);

require_once __DIR__ . '/sessao.php';

function escapar(?string $valor): string
{
    return htmlspecialchars((string)$valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function criar_token_csrf(): string
{
    iniciar_sessao_segura();
    if (empty($_SESSION[CSRF_NOME])) {
        $_SESSION[CSRF_NOME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_NOME];
}

function campo_csrf(): string
{
    return '<input type="hidden" name="csrf_token" value="' . escapar(criar_token_csrf()) . '">';
}

function validar_csrf(): void
{
    iniciar_sessao_segura();
    $tokenEnviado = $_POST['csrf_token'] ?? '';
    $tokenSessao = $_SESSION[CSRF_NOME] ?? '';
    if (!is_string($tokenEnviado) || !is_string($tokenSessao) || !hash_equals($tokenSessao, $tokenEnviado)) {
        http_response_code(403);
        exit('Requisição recusada por falha de segurança CSRF.');
    }
}

function redirecionar_com_mensagem(string $pagina, string $tipo, string $mensagem): void
{
    iniciar_sessao_segura();
    $_SESSION['flash'] = ['tipo' => $tipo, 'mensagem' => $mensagem];
    header('Location: ' . $pagina);
    exit;
}

function obter_flash(): ?array
{
    iniciar_sessao_segura();
    if (empty($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}
