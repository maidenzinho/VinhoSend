<?php
declare(strict_types=1);

final class Vinho
{
    public function __construct(
        public ?int $id,
        public int $usuarioId,
        public string $nome,
        public string $tipo,
        public string $pais,
        public int $safra,
        public float $nota,
        public string $descricao
    ) {}
}
