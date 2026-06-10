<?php
declare(strict_types=1);

require_once __DIR__ . '/../servicos/Validador.php';

assert_equals('Felipe Teste', Validador::nome(' Felipe Teste '), 'Nome válido deve ser aceito e aparado.');
assert_equals('teste@vinhosend.com', Validador::email(' Teste@VinhoSend.com '), 'E-mail deve ser normalizado para minúsculo.');
assert_equals('Senha123', Validador::senha('Senha123'), 'Senha forte deve ser aceita.');
assert_equals(2020, Validador::inteiro('2020', 1900, 2030, 'Safra'), 'Safra válida deve ser aceita.');
assert_equals(8.5, Validador::decimal('8.5', 0, 10, 'Nota'), 'Nota decimal válida deve ser aceita.');

assert_throws(fn() => Validador::nome('AB'), 'Nome curto deve ser recusado.');
assert_throws(fn() => Validador::email('email-invalido'), 'E-mail inválido deve ser recusado.');
assert_throws(fn() => Validador::senha('fraca'), 'Senha fraca deve ser recusada.');
assert_throws(fn() => Validador::inteiro('1800', 1900, 2030, 'Safra'), 'Safra fora do intervalo deve ser recusada.');
assert_throws(fn() => Validador::decimal('11', 0, 10, 'Nota'), 'Nota acima de 10 deve ser recusada.');
