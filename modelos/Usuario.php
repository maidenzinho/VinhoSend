<?php
declare(strict_types=1);

final class Usuario
{
    public function __construct(
        public int $id,
        public string $nome,
        public string $email,
        public string $senhaHash,
        public int $tentativasLogin,
        public ?string $bloqueadoAte
    ) {}
}
