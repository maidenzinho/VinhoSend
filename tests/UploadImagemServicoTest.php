<?php
declare(strict_types=1);

$codigo = file_get_contents(__DIR__ . '/../servicos/UploadImagemServico.php');
assert_true(is_string($codigo), 'Arquivo UploadImagemServico.php deve existir.');
assert_true(str_contains($codigo, 'image/jpeg'), 'Upload deve aceitar JPG.');
assert_true(str_contains($codigo, 'image/png'), 'Upload deve aceitar PNG.');
assert_true(str_contains($codigo, 'image/webp'), 'Upload deve aceitar WEBP.');
assert_true(str_contains($codigo, '2097152'), 'Upload deve limitar a imagem em 2 MB.');
assert_true(str_contains($codigo, 'random_bytes'), 'Nome da imagem deve ser gerado de forma aleatória.');
assert_true(str_contains($codigo, 'move_uploaded_file'), 'Upload deve usar função segura para mover arquivo enviado.');

$caminhoSql = __DIR__ . '/../database/vinhosend_unico.sql';
assert_true(is_file($caminhoSql), 'SQL único deve existir em database/vinhosend_unico.sql.');

$schema = file_get_contents($caminhoSql);
assert_true(is_string($schema), 'SQL único deve ser legível.');

$schemaNormalizado = strtolower(preg_replace('/\s+/', ' ', $schema));

assert_true(str_contains($schemaNormalizado, 'imagem varchar(255)'), 'Tabela vinhos deve ter coluna de imagem.');
assert_true(str_contains($schemaNormalizado, 'create table if not exists compras'), 'Tabela compras deve existir no SQL.');

assert_true(str_contains($schemaNormalizado, 'forma_pagamento varchar(40)'), 'Tabela compras deve guardar forma de pagamento.');
assert_true(str_contains($schemaNormalizado, 'numero_nota varchar(40)'), 'Tabela compras deve guardar número da nota fiscal.');
assert_true(str_contains($schemaNormalizado, 'nota_emitida_em datetime'), 'Tabela compras deve guardar emissão da nota fiscal.');
assert_true(str_contains($schemaNormalizado, 'reservada'), 'Tabela compras deve ter status reservada.');
assert_true(str_contains($schemaNormalizado, 'enviada'), 'Tabela compras deve ter status enviada.');
assert_true(str_contains($schemaNormalizado, 'concluida'), 'Tabela compras deve ter status concluida.');
