<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/seguranca.php';
require_once __DIR__ . '/../repositorios/UsuarioRepositorio.php';
require_once __DIR__ . '/../repositorios/AuditoriaRepositorio.php';
require_once __DIR__ . '/../servicos/Validador.php';

validar_csrf();

try {
    $nome = Validador::nome($_POST['nome'] ?? '');
    $email = Validador::email($_POST['email'] ?? '');
    $senha = Validador::senha($_POST['senha'] ?? '');
    $confirmacao = (string)($_POST['confirmar_senha'] ?? '');

    if (!hash_equals($senha, $confirmacao)) {
        throw new InvalidArgumentException('As senhas não coincidem.');
    }

    $usuarios = new UsuarioRepositorio();
    if ($usuarios->buscarPorEmail($email)) {
        throw new InvalidArgumentException('Já existe uma conta cadastrada com este e-mail.');
    }

    $algoritmo = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_DEFAULT;
    $senhaHash = password_hash($senha, $algoritmo);
    $usuarioId = $usuarios->criar($nome, $email, $senhaHash);
    (new AuditoriaRepositorio())->registrar($usuarioId, 'CADASTRO_USUARIO', 'Usuário cadastrado no VinhoSend.');

    redirecionar_com_mensagem('../login.php', 'sucesso', 'Conta criada com sucesso. Faça login para continuar.');
} catch (Throwable $e) {
    redirecionar_com_mensagem('../registro.php', 'erro', $e->getMessage());
}
