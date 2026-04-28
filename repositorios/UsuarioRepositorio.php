<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/Conexao.php';
require_once __DIR__ . '/../modelos/Usuario.php';

final class UsuarioRepositorio
{
    public function buscarPorEmail(string $email): ?Usuario
    {
        $stmt = Conexao::obter()->prepare('SELECT * FROM usuarios WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $linha = $stmt->fetch();
        return $linha ? $this->mapear($linha) : null;
    }

    public function buscarPorId(int $id): ?Usuario
    {
        $stmt = Conexao::obter()->prepare('SELECT * FROM usuarios WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $linha = $stmt->fetch();
        return $linha ? $this->mapear($linha) : null;
    }

    public function criar(string $nome, string $email, string $senhaHash): int
    {
        $stmt = Conexao::obter()->prepare('INSERT INTO usuarios (nome, email, senha_hash) VALUES (?, ?, ?)');
        $stmt->execute([$nome, $email, $senhaHash]);
        return (int)Conexao::obter()->lastInsertId();
    }

    public function registrarFalha(int $id, int $tentativas, ?string $bloqueadoAte): void
    {
        $stmt = Conexao::obter()->prepare('UPDATE usuarios SET tentativas_login = ?, bloqueado_ate = ? WHERE id = ?');
        $stmt->execute([$tentativas, $bloqueadoAte, $id]);
    }

    public function limparFalhas(int $id): void
    {
        $stmt = Conexao::obter()->prepare('UPDATE usuarios SET tentativas_login = 0, bloqueado_ate = NULL WHERE id = ?');
        $stmt->execute([$id]);
    }

    private function mapear(array $linha): Usuario
    {
        return new Usuario(
            (int)$linha['id'],
            (string)$linha['nome'],
            (string)$linha['email'],
            (string)$linha['senha_hash'],
            (int)$linha['tentativas_login'],
            $linha['bloqueado_ate'] ? (string)$linha['bloqueado_ate'] : null
        );
    }
}
