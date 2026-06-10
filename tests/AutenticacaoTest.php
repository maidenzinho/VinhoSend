<?php
declare(strict_types=1);

$senha = 'SenhaSegura123';
$algoritmo = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_DEFAULT;
$hash = password_hash($senha, $algoritmo);

assert_true($hash !== $senha, 'Senha não pode ser armazenada em texto puro.');
assert_true(password_verify($senha, $hash), 'Senha correta deve validar contra o hash.');
assert_true(!password_verify('SenhaErrada123', $hash), 'Senha incorreta não deve validar contra o hash.');
