<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/Conexao.php';

final class AuditoriaRepositorio
{
    public function registrar(?int $usuarioId, string $acao, string $detalhes = ''): void
    {
        $usuarioId = $this->normalizarUsuarioId($usuarioId);
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'desconhecido';
        $stmt = Conexao::obter()->prepare('INSERT INTO auditoria (usuario_id, acao, detalhes, ip) VALUES (?, ?, ?, ?)');
        $stmt->execute([$usuarioId, $acao, (function_exists('mb_substr') ? mb_substr($detalhes, 0, 255) : substr($detalhes, 0, 255)), $ip]);
    }

    private function normalizarUsuarioId(?int $usuarioId): ?int
    {
        if ($usuarioId === null || $usuarioId <= 0) {
            return null;
        }

        $stmt = Conexao::obter()->prepare('SELECT id FROM usuarios WHERE id = ? LIMIT 1');
        $stmt->execute([$usuarioId]);

        return $stmt->fetchColumn() ? $usuarioId : null;
    }
}
