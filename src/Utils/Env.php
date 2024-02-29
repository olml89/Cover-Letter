<?php

declare(strict_types=1);

namespace olml89\CoverLetter\Utils;

final class Env
{
    public static function get(string $key, mixed $default = null): mixed
    {
        if (!array_key_exists($key, $_ENV)) {
            return null;
        }

        $value = match (strtolower($_ENV[$key])) {
            'true', '(true)' => true,
            'false', '(false)' => false,
            'empty', '(empty)' => '',
            'null', '(null)' => null,
            default => $_ENV[$key],
        };

        return is_null($value) ? $default : $value;
    }
}
