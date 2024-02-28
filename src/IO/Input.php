<?php

namespace olml89\CoverLetter\IO;

use olml89\CoverLetter\ErrorHandling\Exceptions\ValidationException;

final readonly class Input
{
    public function __construct(
        /**
         * @var array<string, string>
         */
        private array $arguments,
    ) {}

    /**
     * @throws ValidationException
     */
    public static function read(array $argv, string ...$argumentNames): self
    {
        $arguments = [];

        for ($i = 0; $i < count($argumentNames); ++$i) {
            $argumentName = $argumentNames[$i];
            $arguments[$argumentName] = $argv[$i + 1] ?? ValidationException::missingArgument($argumentName);
        }

        return new self($arguments);
    }

    /**
     * @throws ValidationException
     */
    public function get(string $argument): string
    {
        return $this->arguments[$argument] ?? ValidationException::missingArgument($argument);
    }
}
