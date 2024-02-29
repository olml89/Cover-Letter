<?php

declare(strict_types=1);

namespace olml89\CoverLetter\ErrorHandling\Exceptions;

use RuntimeException;

final class OutputCreationException extends RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function dir(string $path): self
    {
        return new self(
            sprintf(
                'Error creating directory \'%s\'',
                $path,
            )
        );
    }

    public static function file(string $path): self
    {
        return new self(
            sprintf(
                'Error creating file \'%s\'',
                $path,
            )
        );
    }
}
