<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';

/**
 * Serviço responsável por cifrar e decifrar textos sensíveis do sistema.
 *
 * Uso no projeto: a descrição privada de cada vinho é protegida antes de ser
 * gravada no banco. O algoritmo utilizado é AES-256-GCM, que oferece
 * confidencialidade e verificação de integridade por tag de autenticação.
 */
final class CriptografiaServico
{
    private const PREFIXO = 'v1:';
    private const CIFRA = 'aes-256-gcm';
    private const TAMANHO_IV = 12;
    private const TAMANHO_TAG = 16;

    public static function cifrar(string $textoPlano): string
    {
        if ($textoPlano === '') {
            return '';
        }

        $iv = random_bytes(self::TAMANHO_IV);
        $tag = '';
        $textoCifrado = openssl_encrypt(
            $textoPlano,
            self::CIFRA,
            self::chaveBinaria(),
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            '',
            self::TAMANHO_TAG
        );

        if ($textoCifrado === false) {
            throw new RuntimeException('Não foi possível cifrar a informação sensível.');
        }

        return self::PREFIXO . base64_encode($iv . $tag . $textoCifrado);
    }

    public static function decifrar(string $conteudo): string
    {
        if ($conteudo === '' || !str_starts_with($conteudo, self::PREFIXO)) {
            // Compatibilidade com registros antigos, gravados antes da criptografia.
            return $conteudo;
        }

        $payload = base64_decode(substr($conteudo, strlen(self::PREFIXO)), true);
        if ($payload === false || strlen($payload) <= self::TAMANHO_IV + self::TAMANHO_TAG) {
            throw new RuntimeException('Conteúdo cifrado inválido.');
        }

        $iv = substr($payload, 0, self::TAMANHO_IV);
        $tag = substr($payload, self::TAMANHO_IV, self::TAMANHO_TAG);
        $textoCifrado = substr($payload, self::TAMANHO_IV + self::TAMANHO_TAG);

        $textoPlano = openssl_decrypt(
            $textoCifrado,
            self::CIFRA,
            self::chaveBinaria(),
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        if ($textoPlano === false) {
            throw new RuntimeException('Não foi possível decifrar a informação sensível.');
        }

        return $textoPlano;
    }

    public static function estaCifrado(string $conteudo): bool
    {
        return str_starts_with($conteudo, self::PREFIXO);
    }

    private static function chaveBinaria(): string
    {
        return hash('sha256', CRYPTO_CHAVE, true);
    }
}
