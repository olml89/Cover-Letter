<?php

declare(strict_types=1);

namespace olml89\CoverLetter\Utils;

use olml89\CoverLetter\ErrorHandling\Exceptions\InputReadingException;

trait LoadableFromPath
{
    public static function fromPath(string $path): static
    {
        try {
            $data = require $path;

            return self::fromArray($data);
        }
        catch (Throwable) {
            throw new InputReadingException($path);
        }
    }

    abstract public static function fromArray(array $data): static;
}
