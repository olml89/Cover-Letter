<?php

declare(strict_types=1);

namespace olml89\CoverLetter\ErrorHandling\Exceptions;

use RuntimeException;

final class OutputCreationException extends RuntimeException
{
    public function __construct(string $type, string $path)
    {
        parent::__construct(
            sprintf(
                'Error creating %s \'%s\'',
                $type,
                $path,
            )
        );
    }

    public static function dir(string $dirPath): self
    {
        return new self('directory', $dirPath);
    }

    public static function file(string $filePath): self
    {
        return new self('file', $filePath);
    }
}
