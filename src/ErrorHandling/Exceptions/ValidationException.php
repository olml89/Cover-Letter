<?php

declare(strict_types=1);

namespace olml89\CoverLetter\ErrorHandling\Exceptions;

use InvalidArgumentException;

final class ValidationException extends InvalidArgumentException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function missing(string $inputClass): self
    {
        return new self(
            sprintf(
                'Missing parameter for \'%s\'',
                $inputClass,
            )
        );
    }
}
