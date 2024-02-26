<?php

declare(strict_types=1);

namespace olml89\CoverLetter\Utils;

final class Env
{
    public static function get(string $key, mixed $default = null): mixed
    {
        $value = match (strtolower($_ENV[$key])) {
            'true', '(true)' => true,
            'false', '(false)' => false,
            'empty', '(empty)' => '',
            'null', '(null)' => null,
            default => $_ENV[$key],
        };

        if (is_null($value)) {
            return $default;
        }

        if (preg_match('/\A([\'"])(.*)\1\z/', $value, $matches)) {
            return $matches[2];
        }

        return $value;
    }
}
