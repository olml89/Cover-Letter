<?php

declare(strict_types=1);

namespace olml89\CoverLetter\ReplaceableText;

use olml89\CoverLetter\ErrorHandling\Exceptions\ValidationException;
use Stringable;

abstract readonly class ReplaceableText implements IsReplaceable, Stringable
{
    public function __construct(
        private string $text,
    ) {}

    abstract public function getPlaceholder(): string;

    public function getText(): string
    {
        return $this->text;
    }

    public function __toString(): string
    {
        return $this->getText();
    }
}
