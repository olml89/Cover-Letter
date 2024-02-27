<?php

declare(strict_types=1);

namespace olml89\CoverLetter\ErrorHandling\Exceptions;

use RuntimeException;

final class InputReadingException extends RuntimeException
{
    public function __construct(string $type, string $path)
    {
        parent::__construct(
            sprintf(
                '\'%s\' %s does not exist or it is not readable',
                $type,
                $path,
            )
        );
    }

    public static function dir(string $path): self
    {
        return new self(
            sprintf(
                '\'%s\' directory does not exist or it is not readable',
                $path,
            )
        );
    }

    public static function file(string $path): self
    {
        return new self(
            sprintf(
                '\'%s\' file does not exist or it is not readable',
                $path,
            )
        );
    }

    public static function fileContent(string $path): self
    {
        return new self(
            sprintf(
                'Error getting content from \'%s\'',
                $path,
            )
        );
    }
}
