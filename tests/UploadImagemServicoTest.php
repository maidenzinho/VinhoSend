<?php
declare(strict_types=1);

$codigo = file_get_contents(__DIR__ . '/../servicos/UploadImagemServico.php');
assert_true(str_contains($codigo, 'image/jpeg'), 'Upload deve aceitar JPG.');
assert_true(str_contains($codigo, 'image/png'), 'Upload deve aceitar PNG.');
assert_true(str_contains($codigo, 'image/webp'), 'Upload deve aceitar WEBP.');
assert_true(str_contains($codigo, '2097152'), 'Upload deve limitar a imagem em 2 MB.');
assert_true(str_contains($codigo, 'random_bytes'), 'Nome da imagem deve ser gerado de forma aleatória.');
assert_true(str_contains($codigo, 'move_uploaded_file'), 'Upload deve usar função segura para mover arquivo enviado.');

$schema = file_get_contents(__DIR__ . '/../database/vinhosend_unico.sql');
assert_true(str_contains($schema, 'imagem VARCHAR(255)'), 'Tabela vinhos deve ter coluna de imagem.');
