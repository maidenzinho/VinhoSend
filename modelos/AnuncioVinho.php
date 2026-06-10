<?php
declare(strict_types=1);

final class AnuncioVinho
{
    public function __construct(
        public ?int $id,
        public int $vinhoId,
        public int $vendedorId,
        public string $titulo,
        public float $preco,
        public int $quantidade,
        public string $status,
        public string $observacoes
    ) {}
}
