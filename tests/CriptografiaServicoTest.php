<?php
declare(strict_types=1);

require_once __DIR__ . '/../servicos/CriptografiaServico.php';

$texto = 'Observação privada do vinho: comprado para evento interno.';
$cifrado = CriptografiaServico::cifrar($texto);

assert_true($cifrado !== $texto, 'Texto cifrado não pode ser igual ao texto original.');
assert_true(CriptografiaServico::estaCifrado($cifrado), 'Texto cifrado deve ter prefixo de versão.');
assert_equals($texto, CriptografiaServico::decifrar($cifrado), 'Texto decifrado deve voltar ao conteúdo original.');
assert_equals('', CriptografiaServico::cifrar(''), 'Texto vazio deve permanecer vazio.');
assert_equals('legado sem cifra', CriptografiaServico::decifrar('legado sem cifra'), 'Conteúdo legado sem prefixo deve continuar legível.');
