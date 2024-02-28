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

    public static function missingArgument(string $argument): self
    {
        return new self(
            sprintf(
                'Argument \'%s\' is missing',
                $argument,
            )
        );
    }
}
