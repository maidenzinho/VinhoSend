<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/Conexao.php';

final class AuditoriaRepositorio
{
    public function registrar(?int $usuarioId, string $acao, string $detalhes = ''): void
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'desconhecido';
        $stmt = Conexao::obter()->prepare('INSERT INTO auditoria (usuario_id, acao, detalhes, ip) VALUES (?, ?, ?, ?)');
        $stmt->execute([$usuarioId, $acao, (function_exists('mb_substr') ? mb_substr($detalhes, 0, 255) : substr($detalhes, 0, 255)), $ip]);
    }
}
