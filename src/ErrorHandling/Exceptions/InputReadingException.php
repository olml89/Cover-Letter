<?php

declare(strict_types=1);

namespace olml89\CoverLetter\ErrorHandling\Exceptions;

use RuntimeException;

final class InputReadingException extends RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function require(string $path): self
    {
        return new self(
            sprintf(
                'Required file \'%s\' cannot be imported',
                $path,
            )
        );
    }

    public static function dir(string $path): self
    {
        return new self(
            sprintf(
                'Directory \'%s\' does not exist or it is not readable',
                $path,
            )
        );
    }

    public static function file(string $path): self
    {
        return new self(
            sprintf(
                'File \'%s\' does not exist or it is not readable',
                $path,
            )
        );
    }

    public static function fileContent(string $path): self
    {
        return new self(
            sprintf(
                'Error getting content from file \'%s\'',
                $path,
            )
        );
    }
}
