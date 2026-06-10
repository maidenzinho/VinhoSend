<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/seguranca.php';
require_once __DIR__ . '/../repositorios/AnuncioRepositorio.php';
require_once __DIR__ . '/../repositorios/VinhoRepositorio.php';
require_once __DIR__ . '/../repositorios/AuditoriaRepositorio.php';
require_once __DIR__ . '/../modelos/AnuncioVinho.php';
require_once __DIR__ . '/../servicos/Validador.php';

exigir_login();
validar_csrf();

try {
    $usuarioId = usuario_atual_id();
    $id = isset($_POST['id']) && $_POST['id'] !== '' ? Validador::inteiro($_POST['id'], 1, 999999, 'ID') : null;
    $vinhoId = Validador::inteiro($_POST['vinho_id'] ?? '', 1, 999999, 'Vinho');
    $titulo = Validador::texto($_POST['titulo'] ?? '', 4, 140, 'Título do anúncio');
    $preco = Validador::dinheiro($_POST['preco'] ?? '', 1, 999999.99, 'Preço');
    $quantidade = Validador::inteiro($_POST['quantidade'] ?? '', 1, 9999, 'Quantidade');
    $status = Validador::statusAnuncio($_POST['status'] ?? 'ativo');
    $observacoes = Validador::texto($_POST['observacoes'] ?? '', 0, 1000, 'Observações');

    $vinhoRepo = new VinhoRepositorio();
    if (!$vinhoRepo->buscarDoUsuario($vinhoId, $usuarioId)) {
        throw new InvalidArgumentException('Você só pode anunciar vinhos cadastrados na sua própria conta.');
    }

    $repo = new AnuncioRepositorio();
    $anuncio = new AnuncioVinho($id, $vinhoId, $usuarioId, $titulo, $preco, $quantidade, $status, $observacoes);

    if ($id) {
        if (!$repo->buscarDoVendedor($id, $usuarioId)) {
            throw new InvalidArgumentException('Anúncio não encontrado para este usuário.');
        }
        $repo->atualizar($anuncio);
        (new AuditoriaRepositorio())->registrar($usuarioId, 'ATUALIZAR_ANUNCIO', 'Anúncio atualizado: ' . $titulo);
        redirecionar_com_mensagem('../meus_anuncios.php', 'sucesso', 'Anúncio atualizado com sucesso.');
    }

    $repo->criar($anuncio);
    (new AuditoriaRepositorio())->registrar($usuarioId, 'CRIAR_ANUNCIO', 'Anúncio criado: ' . $titulo);
    redirecionar_com_mensagem('../meus_anuncios.php', 'sucesso', 'Anúncio publicado no marketplace.');
} catch (Throwable $e) {
    redirecionar_com_mensagem('../meus_anuncios.php', 'erro', $e->getMessage());
}
