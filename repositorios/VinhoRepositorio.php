<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/Conexao.php';
require_once __DIR__ . '/../modelos/Vinho.php';

final class VinhoRepositorio
{
    public function listarPorUsuario(int $usuarioId): array
    {
        $stmt = Conexao::obter()->prepare('SELECT * FROM vinhos WHERE usuario_id = ? ORDER BY criado_em DESC, id DESC');
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll();
    }

    public function buscarDoUsuario(int $id, int $usuarioId): ?array
    {
        $stmt = Conexao::obter()->prepare('SELECT * FROM vinhos WHERE id = ? AND usuario_id = ? LIMIT 1');
        $stmt->execute([$id, $usuarioId]);
        $linha = $stmt->fetch();
        return $linha ?: null;
    }

    public function criar(Vinho $vinho): void
    {
        $sql = 'INSERT INTO vinhos (usuario_id, nome, tipo, pais, safra, nota, descricao) VALUES (?, ?, ?, ?, ?, ?, ?)';
        $stmt = Conexao::obter()->prepare($sql);
        $stmt->execute([$vinho->usuarioId, $vinho->nome, $vinho->tipo, $vinho->pais, $vinho->safra, $vinho->nota, $vinho->descricao]);
    }

    public function atualizar(Vinho $vinho): void
    {
        $sql = 'UPDATE vinhos SET nome = ?, tipo = ?, pais = ?, safra = ?, nota = ?, descricao = ?, atualizado_em = NOW() WHERE id = ? AND usuario_id = ?';
        $stmt = Conexao::obter()->prepare($sql);
        $stmt->execute([$vinho->nome, $vinho->tipo, $vinho->pais, $vinho->safra, $vinho->nota, $vinho->descricao, $vinho->id, $vinho->usuarioId]);
    }

    public function excluir(int $id, int $usuarioId): void
    {
        $stmt = Conexao::obter()->prepare('DELETE FROM vinhos WHERE id = ? AND usuario_id = ?');
        $stmt->execute([$id, $usuarioId]);
    }
}
