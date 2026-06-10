<?php
declare(strict_types=1);

final class Validador
{
    public static function nome(string $valor): string
    {
        $valor = trim($valor);
        if (self::comprimento($valor) < 3 || self::comprimento($valor) > 120) {
            throw new InvalidArgumentException('Informe um nome entre 3 e 120 caracteres.');
        }
        return $valor;
    }

    public static function email(string $valor): string
    {
        $valor = self::minusculo(trim($valor));
        if (!filter_var($valor, FILTER_VALIDATE_EMAIL) || self::comprimento($valor) > 180) {
            throw new InvalidArgumentException('Informe um e-mail válido.');
        }
        return $valor;
    }

    public static function senha(string $valor): string
    {
        if (self::comprimento($valor) < 8 || !preg_match('/[A-Z]/', $valor) || !preg_match('/[a-z]/', $valor) || !preg_match('/[0-9]/', $valor)) {
            throw new InvalidArgumentException('A senha deve ter pelo menos 8 caracteres, letra maiúscula, letra minúscula e número.');
        }
        return $valor;
    }

    public static function texto(string $valor, int $min, int $max, string $campo): string
    {
        $valor = trim($valor);
        if (self::comprimento($valor) < $min || self::comprimento($valor) > $max) {
            throw new InvalidArgumentException($campo . ' deve ter entre ' . $min . ' e ' . $max . ' caracteres.');
        }
        return $valor;
    }

    public static function inteiro(string|int $valor, int $min, int $max, string $campo): int
    {
        $int = filter_var($valor, FILTER_VALIDATE_INT);
        if ($int === false || $int < $min || $int > $max) {
            throw new InvalidArgumentException($campo . ' inválido.');
        }
        return $int;
    }

    public static function decimal(string|float $valor, float $min, float $max, string $campo): float
    {
        $decimal = filter_var($valor, FILTER_VALIDATE_FLOAT);
        if ($decimal === false || $decimal < $min || $decimal > $max) {
            throw new InvalidArgumentException($campo . ' inválida.');
        }
        return (float)$decimal;
    }

    public static function dinheiro(string|float $valor, float $min, float $max, string $campo): float
    {
        if (is_string($valor)) {
            $valor = str_replace(',', '.', trim($valor));
        }
        $decimal = filter_var($valor, FILTER_VALIDATE_FLOAT);
        if ($decimal === false || $decimal < $min || $decimal > $max) {
            throw new InvalidArgumentException($campo . ' deve estar entre R$ ' . number_format($min, 2, ',', '.') . ' e R$ ' . number_format($max, 2, ',', '.') . '.');
        }
        return round((float)$decimal, 2);
    }

    public static function statusAnuncio(string $valor): string
    {
        $valor = trim($valor);
        $permitidos = ['ativo', 'pausado'];
        if (!in_array($valor, $permitidos, true)) {
            throw new InvalidArgumentException('Status do anúncio inválido.');
        }
        return $valor;
    }


    private static function comprimento(string $valor): int
    {
        return function_exists('mb_strlen') ? mb_strlen($valor) : strlen($valor);
    }

    private static function minusculo(string $valor): string
    {
        return function_exists('mb_strtolower') ? mb_strtolower($valor) : strtolower($valor);
    }
}
