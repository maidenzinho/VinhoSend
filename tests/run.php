<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$arquivos = glob(__DIR__ . '/*Test.php') ?: [];
sort($arquivos);
$total = 0;

foreach ($arquivos as $arquivo) {
    require $arquivo;
    $total++;
    echo '[OK] ' . basename($arquivo) . PHP_EOL;
}

echo PHP_EOL . 'Testes executados com sucesso: ' . $total . PHP_EOL;
