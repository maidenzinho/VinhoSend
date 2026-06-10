<?php
declare(strict_types=1);

final class UploadImagemServico
{
    private const TAMANHO_MAXIMO = 2097152; // 2 MB
    private const TIPOS_PERMITIDOS = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp'
    ];

    public static function salvarImagemVinho(array $arquivo, ?string $imagemAtual = null): ?string
    {
        if (($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return $imagemAtual;
        }

        if (($arquivo['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new InvalidArgumentException('Não foi possível enviar a foto do vinho.');
        }

        if ((int)($arquivo['size'] ?? 0) > self::TAMANHO_MAXIMO) {
            throw new InvalidArgumentException('A foto deve ter no máximo 2 MB.');
        }

        $tmp = (string)($arquivo['tmp_name'] ?? '');
        if ($tmp === '' || !is_uploaded_file($tmp)) {
            throw new InvalidArgumentException('Arquivo de imagem inválido.');
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($tmp);
        if (!isset(self::TIPOS_PERMITIDOS[$mime])) {
            throw new InvalidArgumentException('A foto precisa estar em JPG, PNG ou WEBP.');
        }

        $pasta = __DIR__ . '/../uploads/vinhos';
        if (!is_dir($pasta)) {
            mkdir($pasta, 0755, true);
        }

        $extensao = self::TIPOS_PERMITIDOS[$mime];
        $nomeArquivo = 'vinho_' . bin2hex(random_bytes(16)) . '.' . $extensao;
        $destino = $pasta . '/' . $nomeArquivo;

        if (!move_uploaded_file($tmp, $destino)) {
            throw new RuntimeException('Não foi possível salvar a foto enviada.');
        }

        if ($imagemAtual) {
            self::removerImagem($imagemAtual);
        }

        return 'uploads/vinhos/' . $nomeArquivo;
    }

    public static function removerImagem(?string $caminho): void
    {
        if (!$caminho || !str_starts_with($caminho, 'uploads/vinhos/')) {
            return;
        }

        $arquivo = realpath(__DIR__ . '/../' . $caminho);
        $pastaPermitida = realpath(__DIR__ . '/../uploads/vinhos');

        if ($arquivo && $pastaPermitida && str_starts_with($arquivo, $pastaPermitida) && is_file($arquivo)) {
            unlink($arquivo);
        }
    }
}
