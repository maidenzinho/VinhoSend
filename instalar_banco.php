<?php
declare(strict_types=1);

require_once __DIR__ . '/config/config.php';

$dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORTA . ';charset=utf8mb4';
$pdo = new PDO($dsn, DB_USUARIO, DB_SENHA, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$sql = file_get_contents(__DIR__ . '/database/schema.sql');
$pdo->exec($sql);

echo '<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><title>Banco instalado</title><link rel="stylesheet" href="styles.css"></head><body><main class="auth-main"><section class="auth-card"><h1>Banco instalado com sucesso</h1><p>O banco vinhosend_ra2 e as tabelas foram criados no MySQL do XAMPP.</p><a class="pill-btn pill-primary" href="index.html">Voltar para o início</a></section></main></body></html>';
