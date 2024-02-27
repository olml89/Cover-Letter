<?php

declare(strict_types=1);

namespace olml89\CoverLetter\Utils;

use olml89\CoverLetter\ErrorHandling\Exceptions\InputReadingException;

trait RequiresArrayConfigurationFile
{
    /**
     * @throws InputReadingException
     */
    private static function requireArrayConfigurationFile(string $path): array
    {
        try {
            return require $path;
        }
        catch (Throwable) {
            throw new InputReadingException($path);
        }
    }
}
