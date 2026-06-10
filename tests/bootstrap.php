<?php
declare(strict_types=1);

function assert_true(bool $condicao, string $mensagem): void
{
    if (!$condicao) {
        throw new RuntimeException($mensagem);
    }
}

function assert_equals(mixed $esperado, mixed $atual, string $mensagem): void
{
    if ($esperado !== $atual) {
        throw new RuntimeException($mensagem . ' Esperado: ' . var_export($esperado, true) . ' Atual: ' . var_export($atual, true));
    }
}

function assert_throws(callable $callback, string $mensagem): void
{
    try {
        $callback();
    } catch (Throwable) {
        return;
    }
    throw new RuntimeException($mensagem);
}
