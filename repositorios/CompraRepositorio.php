<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/Conexao.php';
require_once __DIR__ . '/../modelos/Compra.php';
require_once __DIR__ . '/../servicos/CriptografiaServico.php';

final class CompraRepositorio
{
    public function reservarCompra(Compra $compra): int
    {
        $pdo = Conexao::obter();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare('SELECT id, vendedor_id, preco, quantidade, status FROM anuncios_vinhos WHERE id = ? FOR UPDATE');
            $stmt->execute([$compra->anuncioId]);
            $anuncio = $stmt->fetch();

            if (!$anuncio || $anuncio['status'] !== 'ativo') {
                throw new RuntimeException('Anúncio indisponível.');
            }
            if ((int)$anuncio['vendedor_id'] === $compra->compradorId) {
                throw new RuntimeException('Você não pode comprar seu próprio anúncio.');
            }
            if ((int)$anuncio['quantidade'] < $compra->quantidade) {
                throw new RuntimeException('Quantidade indisponível para este anúncio.');
            }

            $precoUnitario = (float)$anuncio['preco'];
            $total = round($precoUnitario * $compra->quantidade, 2);
            $numeroNota = $this->gerarNumeroNota();

            $insert = $pdo->prepare('INSERT INTO compras
                (anuncio_id, comprador_id, vendedor_id, quantidade, preco_unitario, total, forma_pagamento, endereco_entrega, numero_nota, nota_emitida_em, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)');
            $insert->execute([
                $compra->anuncioId,
                $compra->compradorId,
                (int)$anuncio['vendedor_id'],
                $compra->quantidade,
                $precoUnitario,
                $total,
                $compra->formaPagamento,
                CriptografiaServico::cifrar($compra->enderecoEntrega),
                $numeroNota,
                $compra->status
            ]);

            $compraId = (int)$pdo->lastInsertId();
            $novoEstoque = (int)$anuncio['quantidade'] - $compra->quantidade;
            $novoStatus = $novoEstoque === 0 ? 'vendido' : 'ativo';
            $update = $pdo->prepare('UPDATE anuncios_vinhos SET quantidade = ?, status = ?, atualizado_em = NOW() WHERE id = ?');
            $update->execute([$novoEstoque, $novoStatus, $compra->anuncioId]);

            $pdo->commit();
            return $compraId;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    public function listarPorComprador(int $compradorId): array
    {
        $sql = 'SELECT c.*, a.titulo, v.nome AS vinho_nome, v.imagem, u.nome AS vendedor_nome
                FROM compras c
                INNER JOIN anuncios_vinhos a ON a.id = c.anuncio_id
                INNER JOIN vinhos v ON v.id = a.vinho_id
                INNER JOIN usuarios u ON u.id = c.vendedor_id
                WHERE c.comprador_id = ?
                ORDER BY c.criado_em DESC, c.id DESC';
        $stmt = Conexao::obter()->prepare($sql);
        $stmt->execute([$compradorId]);
        return array_map([$this, 'prepararLinha'], $stmt->fetchAll());
    }

    public function listarVendasPorVendedor(int $vendedorId): array
    {
        $sql = 'SELECT c.*, a.titulo, v.nome AS vinho_nome, v.imagem, u.nome AS comprador_nome, u.email AS comprador_email
                FROM compras c
                INNER JOIN anuncios_vinhos a ON a.id = c.anuncio_id
                INNER JOIN vinhos v ON v.id = a.vinho_id
                INNER JOIN usuarios u ON u.id = c.comprador_id
                WHERE c.vendedor_id = ?
                ORDER BY c.criado_em DESC, c.id DESC';
        $stmt = Conexao::obter()->prepare($sql);
        $stmt->execute([$vendedorId]);
        return array_map([$this, 'prepararLinha'], $stmt->fetchAll());
    }

    public function buscarNotaFiscal(int $compraId, int $usuarioId): ?array
    {
        $sql = 'SELECT c.*, a.titulo, v.nome AS vinho_nome, v.tipo, v.safra, v.imagem,
                       comprador.nome AS comprador_nome, comprador.email AS comprador_email,
                       vendedor.nome AS vendedor_nome, vendedor.email AS vendedor_email
                FROM compras c
                INNER JOIN anuncios_vinhos a ON a.id = c.anuncio_id
                INNER JOIN vinhos v ON v.id = a.vinho_id
                INNER JOIN usuarios comprador ON comprador.id = c.comprador_id
                INNER JOIN usuarios vendedor ON vendedor.id = c.vendedor_id
                WHERE c.id = ? AND (c.comprador_id = ? OR c.vendedor_id = ?)
                LIMIT 1';
        $stmt = Conexao::obter()->prepare($sql);
        $stmt->execute([$compraId, $usuarioId, $usuarioId]);
        $linha = $stmt->fetch();
        return $linha ? $this->prepararLinha($linha) : null;
    }

    public function contarSolicitacoesPendentes(int $vendedorId): int
    {
        $stmt = Conexao::obter()->prepare('SELECT COUNT(*) FROM compras WHERE vendedor_id = ? AND status = ?');
        $stmt->execute([$vendedorId, 'reservada']);
        return (int)$stmt->fetchColumn();
    }

    public function marcarComoEnviada(int $compraId, int $vendedorId): bool
    {
        $stmt = Conexao::obter()->prepare('UPDATE compras SET status = ? WHERE id = ? AND vendedor_id = ? AND status = ?');
        $stmt->execute(['enviada', $compraId, $vendedorId, 'reservada']);
        return $stmt->rowCount() > 0;
    }

    public function concluirPeloComprador(int $compraId, int $compradorId): bool
    {
        $stmt = Conexao::obter()->prepare('UPDATE compras SET status = ? WHERE id = ? AND comprador_id = ? AND status = ?');
        $stmt->execute(['concluida', $compraId, $compradorId, 'enviada']);
        return $stmt->rowCount() > 0;
    }

    private function prepararLinha(array $linha): array
    {
        $linha['endereco_entrega'] = CriptografiaServico::decifrar((string)($linha['endereco_entrega'] ?? ''));
        $linha['status_texto'] = match ($linha['status'] ?? '') {
            'reservada' => 'Aguardando envio',
            'enviada' => 'Enviado pelo vendedor',
            'concluida' => 'Concluída',
            'cancelada' => 'Cancelada',
            default => (string)($linha['status'] ?? 'Indefinido')
        };
        $linha['forma_pagamento'] = (string)($linha['forma_pagamento'] ?? 'Pix');
        $linha['numero_nota'] = (string)($linha['numero_nota'] ?? '');
        return $linha;
    }

    private function gerarNumeroNota(): string
    {
        return 'NF-' . date('Ymd') . '-' . str_pad((string)random_int(1, 999999), 6, '0', STR_PAD_LEFT);
    }
}
