<?php

declare(strict_types=1);

namespace olml89\CoverLetter\ReplaceableText;

final readonly class Position extends ReplaceableText
{
    private const string PLACEHOLDER = '[_position_]';

    public function getPlaceholder(): string
    {
        return self::PLACEHOLDER;
    }
}
