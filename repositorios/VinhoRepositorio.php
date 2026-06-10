<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/Conexao.php';
require_once __DIR__ . '/../modelos/Vinho.php';
require_once __DIR__ . '/../servicos/CriptografiaServico.php';

final class VinhoRepositorio
{
    public function listarPorUsuario(int $usuarioId): array
    {
        $stmt = Conexao::obter()->prepare('SELECT * FROM vinhos WHERE usuario_id = ? ORDER BY criado_em DESC, id DESC');
        $stmt->execute([$usuarioId]);
        $linhas = $stmt->fetchAll();
        return array_map([$this, 'decifrarDescricao'], $linhas);
    }

    public function buscarDoUsuario(int $id, int $usuarioId): ?array
    {
        $stmt = Conexao::obter()->prepare('SELECT * FROM vinhos WHERE id = ? AND usuario_id = ? LIMIT 1');
        $stmt->execute([$id, $usuarioId]);
        $linha = $stmt->fetch();
        return $linha ? $this->decifrarDescricao($linha) : null;
    }

    public function criar(Vinho $vinho): void
    {
        // Requisito: dado sensível protegido. A descrição é cifrada antes de ir para o banco.
        $sql = 'INSERT INTO vinhos (usuario_id, nome, tipo, pais, safra, nota, descricao, imagem) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = Conexao::obter()->prepare($sql);
        $stmt->execute([
            $vinho->usuarioId,
            $vinho->nome,
            $vinho->tipo,
            $vinho->pais,
            $vinho->safra,
            $vinho->nota,
            CriptografiaServico::cifrar($vinho->descricao),
            $vinho->imagem
        ]);
    }

    public function atualizar(Vinho $vinho): void
    {
        $sql = 'UPDATE vinhos SET nome = ?, tipo = ?, pais = ?, safra = ?, nota = ?, descricao = ?, imagem = ?, atualizado_em = NOW() WHERE id = ? AND usuario_id = ?';
        $stmt = Conexao::obter()->prepare($sql);
        $stmt->execute([
            $vinho->nome,
            $vinho->tipo,
            $vinho->pais,
            $vinho->safra,
            $vinho->nota,
            CriptografiaServico::cifrar($vinho->descricao),
            $vinho->imagem,
            $vinho->id,
            $vinho->usuarioId
        ]);
    }

    public function excluir(int $id, int $usuarioId): ?string
    {
        $vinho = $this->buscarDoUsuario($id, $usuarioId);
        if (!$vinho) {
            return null;
        }

        $stmt = Conexao::obter()->prepare('DELETE FROM vinhos WHERE id = ? AND usuario_id = ?');
        $stmt->execute([$id, $usuarioId]);
        return $vinho['imagem'] ?? null;
    }

    private function decifrarDescricao(array $linha): array
    {
        $linha['descricao'] = CriptografiaServico::decifrar((string)($linha['descricao'] ?? ''));
        return $linha;
    }
}
