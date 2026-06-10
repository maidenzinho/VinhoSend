<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/seguranca.php';
require_once __DIR__ . '/../repositorios/VinhoRepositorio.php';
require_once __DIR__ . '/../repositorios/AuditoriaRepositorio.php';
require_once __DIR__ . '/../modelos/Vinho.php';
require_once __DIR__ . '/../servicos/Validador.php';
require_once __DIR__ . '/../servicos/UploadImagemServico.php';

exigir_login();
validar_csrf();

try {
    $usuarioId = usuario_atual_id();
    $id = isset($_POST['id']) && $_POST['id'] !== '' ? Validador::inteiro($_POST['id'], 1, 999999, 'ID') : null;
    $nome = Validador::texto($_POST['nome'] ?? '', 2, 120, 'Nome do vinho');
    $tipo = Validador::texto($_POST['tipo'] ?? '', 3, 60, 'Tipo');
    $pais = Validador::texto($_POST['pais'] ?? '', 2, 80, 'País');
    $safra = Validador::inteiro($_POST['safra'] ?? '', 1900, (int)date('Y'), 'Safra');
    $nota = Validador::decimal($_POST['nota'] ?? '', 0, 10, 'Nota');
    $descricao = Validador::texto($_POST['descricao'] ?? '', 0, 1000, 'Descrição');

    $repo = new VinhoRepositorio();
    $imagemAtual = null;

    if ($id) {
        $vinhoExistente = $repo->buscarDoUsuario($id, $usuarioId);
        if (!$vinhoExistente) {
            throw new InvalidArgumentException('Vinho não encontrado para este usuário.');
        }
        $imagemAtual = $vinhoExistente['imagem'] ?? null;
    }

    // Requisito: upload seguro. Só JPG, PNG e WEBP até 2 MB são aceitos.
    $imagem = UploadImagemServico::salvarImagemVinho($_FILES['imagem'] ?? [], $imagemAtual);
    $vinho = new Vinho($id, $usuarioId, $nome, $tipo, $pais, $safra, $nota, $descricao, $imagem);

    if ($id) {
        $repo->atualizar($vinho);
        (new AuditoriaRepositorio())->registrar($usuarioId, 'ATUALIZAR_VINHO', 'Vinho atualizado: ' . $nome);
        redirecionar_com_mensagem('../painel.php', 'sucesso', 'Vinho atualizado com sucesso.');
    }

    $repo->criar($vinho);
    (new AuditoriaRepositorio())->registrar($usuarioId, 'CRIAR_VINHO', 'Vinho cadastrado: ' . $nome);
    redirecionar_com_mensagem('../painel.php', 'sucesso', 'Vinho cadastrado com sucesso.');
} catch (Throwable $e) {
    redirecionar_com_mensagem('../painel.php', 'erro', $e->getMessage());
}
