<?php
declare(strict_types=1);

$codigo = file_get_contents(__DIR__ . '/../repositorios/VinhoRepositorio.php');
assert_true(str_contains($codigo, 'WHERE id = ? AND usuario_id = ?'), 'Operações de vinho devem filtrar por id e usuario_id.');
assert_true(str_contains($codigo, 'prepare('), 'Repositório deve usar prepared statements.');
assert_true(str_contains($codigo, 'CriptografiaServico::cifrar'), 'Repositório deve cifrar a descrição antes de persistir.');
assert_true(str_contains($codigo, 'CriptografiaServico::decifrar'), 'Repositório deve decifrar a descrição ao listar/buscar.');
