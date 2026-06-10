<?php
declare(strict_types=1);

require_once __DIR__ . '/../servicos/Validador.php';
require_once __DIR__ . '/../modelos/AnuncioVinho.php';
require_once __DIR__ . '/../modelos/Compra.php';

assert_equals(79.90, Validador::dinheiro('79,90', 1, 999999, 'Preço'), 'Preço com vírgula deve ser aceito e normalizado.');
assert_equals('ativo', Validador::statusAnuncio('ativo'), 'Status ativo deve ser aceito.');
assert_equals('pausado', Validador::statusAnuncio('pausado'), 'Status pausado deve ser aceito.');
assert_throws(fn() => Validador::dinheiro('0', 1, 999999, 'Preço'), 'Preço zerado deve ser recusado.');
assert_throws(fn() => Validador::statusAnuncio('excluido'), 'Status fora da regra deve ser recusado.');

$anuncio = new AnuncioVinho(null, 10, 2, 'Malbec reservado', 120.00, 3, 'ativo', 'Conservado em adega climatizada.');
assert_equals(10, $anuncio->vinhoId, 'Anúncio deve estar vinculado a um vinho.');
assert_equals(2, $anuncio->vendedorId, 'Anúncio deve guardar o vendedor dono.');
assert_equals(3, $anuncio->quantidade, 'Anúncio deve controlar estoque.');

$compra = new Compra(null, 5, 8, 2, 2, 120.00, 240.00, 'Retirada combinada com o vendedor.');
assert_equals(8, $compra->compradorId, 'Compra deve registrar comprador.');
assert_equals(2, $compra->vendedorId, 'Compra deve registrar vendedor.');
assert_equals(240.00, $compra->total, 'Compra deve registrar total calculado.');
