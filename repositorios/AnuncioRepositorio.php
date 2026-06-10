<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/Conexao.php';
require_once __DIR__ . '/../modelos/AnuncioVinho.php';
require_once __DIR__ . '/../servicos/CriptografiaServico.php';

final class AnuncioRepositorio
{
    public function listarMarketplace(int $usuarioId): array
    {
        $sql = 'SELECT a.*, v.nome AS vinho_nome, v.tipo, v.pais, v.safra, v.imagem, u.nome AS vendedor_nome
                FROM anuncios_vinhos a
                INNER JOIN vinhos v ON v.id = a.vinho_id
                INNER JOIN usuarios u ON u.id = a.vendedor_id
                WHERE a.status = ? AND a.quantidade > 0 AND a.vendedor_id <> ?
                ORDER BY a.criado_em DESC, a.id DESC';
        $stmt = Conexao::obter()->prepare($sql);
        $stmt->execute(['ativo', $usuarioId]);
        return array_map([$this, 'decifrarObservacoes'], $stmt->fetchAll());
    }

    public function listarPorVendedor(int $vendedorId): array
    {
        $sql = 'SELECT a.*, v.nome AS vinho_nome, v.tipo, v.pais, v.safra, v.imagem
                FROM anuncios_vinhos a
                INNER JOIN vinhos v ON v.id = a.vinho_id
                WHERE a.vendedor_id = ?
                ORDER BY a.criado_em DESC, a.id DESC';
        $stmt = Conexao::obter()->prepare($sql);
        $stmt->execute([$vendedorId]);
        return array_map([$this, 'decifrarObservacoes'], $stmt->fetchAll());
    }

    public function buscarDoVendedor(int $id, int $vendedorId): ?array
    {
        $sql = 'SELECT a.*, v.nome AS vinho_nome, v.tipo, v.pais, v.safra, v.imagem
                FROM anuncios_vinhos a
                INNER JOIN vinhos v ON v.id = a.vinho_id
                WHERE a.id = ? AND a.vendedor_id = ? LIMIT 1';
        $stmt = Conexao::obter()->prepare($sql);
        $stmt->execute([$id, $vendedorId]);
        $linha = $stmt->fetch();
        return $linha ? $this->decifrarObservacoes($linha) : null;
    }

    public function criar(AnuncioVinho $anuncio): void
    {
        $sql = 'INSERT INTO anuncios_vinhos (vinho_id, vendedor_id, titulo, preco, quantidade, status, observacoes)
                VALUES (?, ?, ?, ?, ?, ?, ?)';
        $stmt = Conexao::obter()->prepare($sql);
        $stmt->execute([
            $anuncio->vinhoId,
            $anuncio->vendedorId,
            $anuncio->titulo,
            $anuncio->preco,
            $anuncio->quantidade,
            $anuncio->status,
            CriptografiaServico::cifrar($anuncio->observacoes)
        ]);
    }

    public function atualizar(AnuncioVinho $anuncio): void
    {
        $sql = 'UPDATE anuncios_vinhos
                SET vinho_id = ?, titulo = ?, preco = ?, quantidade = ?, status = ?, observacoes = ?, atualizado_em = NOW()
                WHERE id = ? AND vendedor_id = ?';
        $stmt = Conexao::obter()->prepare($sql);
        $stmt->execute([
            $anuncio->vinhoId,
            $anuncio->titulo,
            $anuncio->preco,
            $anuncio->quantidade,
            $anuncio->status,
            CriptografiaServico::cifrar($anuncio->observacoes),
            $anuncio->id,
            $anuncio->vendedorId
        ]);
    }

    public function pausar(int $id, int $vendedorId): void
    {
        $stmt = Conexao::obter()->prepare('UPDATE anuncios_vinhos SET status = ?, atualizado_em = NOW() WHERE id = ? AND vendedor_id = ?');
        $stmt->execute(['pausado', $id, $vendedorId]);
    }

    public function buscarDisponivelParaCompra(int $id, int $compradorId): ?array
    {
        $sql = 'SELECT a.*, v.nome AS vinho_nome, v.tipo, v.pais, v.safra, v.imagem, u.nome AS vendedor_nome
                FROM anuncios_vinhos a
                INNER JOIN vinhos v ON v.id = a.vinho_id
                INNER JOIN usuarios u ON u.id = a.vendedor_id
                WHERE a.id = ? AND a.status = ? AND a.quantidade > 0 AND a.vendedor_id <> ?
                LIMIT 1';
        $stmt = Conexao::obter()->prepare($sql);
        $stmt->execute([$id, 'ativo', $compradorId]);
        $linha = $stmt->fetch();
        return $linha ? $this->decifrarObservacoes($linha) : null;
    }

    private function decifrarObservacoes(array $linha): array
    {
        $linha['observacoes'] = CriptografiaServico::decifrar((string)($linha['observacoes'] ?? ''));
        return $linha;
    }
}
