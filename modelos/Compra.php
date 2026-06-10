<?php
declare(strict_types=1);

final class Compra
{
    public function __construct(
        public ?int $id,
        public int $anuncioId,
        public int $compradorId,
        public int $vendedorId,
        public int $quantidade,
        public float $precoUnitario,
        public float $total,
        public string $enderecoEntrega,
        public string $status = 'reservada'
    ) {}
}
