<?php

class validador {

    // Verifica que el valor esté definido y no sea vacío (ni espacios)
    public static function requerido($valor): bool {
        return isset($valor) && trim((string)$valor) !== '';
    }

    // Valida que el string sea un email válido
    public static function email(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    // Verifica que el texto tenga una longitud entre mínimo y máximo
    public static function longitud(string $texto, int $min = 0, int $max = PHP_INT_MAX): bool {
        $l = mb_strlen($texto); // Compatible con UTF-8
        return $l >= $min && $l <= $max;
    }

    // Verifica que el valor sea un número entero positivo (>= 1)
    public static function enteroPositivo($valor): bool {
        return filter_var($valor, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) !== false;
    }
}
