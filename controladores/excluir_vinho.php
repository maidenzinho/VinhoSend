<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/seguranca.php';
require_once __DIR__ . '/../repositorios/VinhoRepositorio.php';
require_once __DIR__ . '/../repositorios/AuditoriaRepositorio.php';
require_once __DIR__ . '/../servicos/Validador.php';
require_once __DIR__ . '/../servicos/UploadImagemServico.php';

exigir_login();
validar_csrf();

try {
    $usuarioId = usuario_atual_id();
    $id = Validador::inteiro($_POST['id'] ?? '', 1, 999999, 'ID');

    $repo = new VinhoRepositorio();
    $imagem = $repo->excluir($id, $usuarioId);
    UploadImagemServico::removerImagem($imagem);

    (new AuditoriaRepositorio())->registrar($usuarioId, 'EXCLUIR_VINHO', 'Vinho removido da adega.');
    redirecionar_com_mensagem('../painel.php', 'sucesso', 'Vinho excluído com sucesso.');
} catch (Throwable $e) {
    redirecionar_com_mensagem('../painel.php', 'erro', $e->getMessage());
}
