<?php
declare(strict_types=1);

$paginasProtegidas = [
    'painel.php',
    'marketplace.php',
    'meus_anuncios.php',
    'minhas_compras.php',
];

foreach ($paginasProtegidas as $pagina) {
    $codigo = file_get_contents(__DIR__ . '/../' . $pagina);
    assert_true(str_contains($codigo, 'exigir_login()'), 'Página ' . $pagina . ' deve exigir login.');
}

$controladoresProtegidos = [
    'comprar_anuncio.php',
    'excluir_vinho.php',
    'pausar_anuncio.php',
    'salvar_anuncio.php',
    'salvar_vinho.php',
];

foreach ($controladoresProtegidos as $controladorProtegido) {
    $codigo = file_get_contents(__DIR__ . '/../controladores/' . $controladorProtegido);
    assert_true(str_contains($codigo, 'exigir_login()'), 'Controlador ' . $controladorProtegido . ' deve exigir login antes da ação.');
}

$logout = file_get_contents(__DIR__ . '/../controladores/sair.php');
assert_true(str_contains($logout, 'REQUEST_METHOD'), 'Logout deve recusar acesso direto por GET.');
assert_true(str_contains($logout, 'limpar_sessao_local()'), 'Logout deve limpar sessão mesmo quando ela estiver antiga.');

$sessao = file_get_contents(__DIR__ . '/../config/sessao.php');
assert_true(str_contains($sessao, 'usuario_existe_no_banco'), 'Sessão deve conferir se o usuário ainda existe no banco.');
assert_true(str_contains($sessao, 'SELECT id FROM usuarios'), 'Sessão deve consultar o usuário real antes de liberar acesso.');

$auditoria = file_get_contents(__DIR__ . '/../repositorios/AuditoriaRepositorio.php');
assert_true(str_contains($auditoria, 'normalizarUsuarioId'), 'Auditoria deve normalizar usuário para evitar falha de chave estrangeira.');
assert_true(str_contains($auditoria, 'SELECT id FROM usuarios'), 'Auditoria deve verificar se o usuário existe antes de inserir o log.');
