<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/seguranca.php';
require_once __DIR__ . '/../repositorios/UsuarioRepositorio.php';
require_once __DIR__ . '/../repositorios/AuditoriaRepositorio.php';
require_once __DIR__ . '/../servicos/Validador.php';

validar_csrf();

try {
    $email = Validador::email($_POST['email'] ?? '');
    $senha = (string)($_POST['senha'] ?? '');
    $usuarios = new UsuarioRepositorio();
    $auditoria = new AuditoriaRepositorio();
    $usuario = $usuarios->buscarPorEmail($email);

    if (!$usuario) {
        $auditoria->registrar(null, 'LOGIN_FALHA', 'Tentativa com e-mail inexistente: ' . $email);
        throw new InvalidArgumentException('E-mail ou senha inválidos.');
    }

    if ($usuario->bloqueadoAte && strtotime($usuario->bloqueadoAte) > time()) {
        $auditoria->registrar($usuario->id, 'LOGIN_BLOQUEADO', 'Conta bloqueada temporariamente por tentativas inválidas.');
        throw new InvalidArgumentException('Conta temporariamente bloqueada. Tente novamente mais tarde.');
    }

    if (!password_verify($senha, $usuario->senhaHash)) {
        $tentativas = $usuario->tentativasLogin + 1;
        $bloqueadoAte = null;
        if ($tentativas >= MAX_TENTATIVAS_LOGIN) {
            $bloqueadoAte = date('Y-m-d H:i:s', time() + (MINUTOS_BLOQUEIO_LOGIN * 60));
        }
        $usuarios->registrarFalha($usuario->id, $tentativas, $bloqueadoAte);
        $auditoria->registrar($usuario->id, 'LOGIN_FALHA', 'Senha incorreta. Tentativa ' . $tentativas . '.');
        throw new InvalidArgumentException('E-mail ou senha inválidos.');
    }

    $usuarios->limparFalhas($usuario->id);
    session_regenerate_id(true);
    $_SESSION['usuario_id'] = $usuario->id;
    $_SESSION['usuario_nome'] = $usuario->nome;
    $_SESSION['usuario_email'] = $usuario->email;
    $auditoria->registrar($usuario->id, 'LOGIN_SUCESSO', 'Usuário autenticado.');
    redirecionar_com_mensagem('../painel.php', 'sucesso', 'Login realizado com sucesso.');
} catch (Throwable $e) {
    redirecionar_com_mensagem('../login.php', 'erro', $e->getMessage());
}
