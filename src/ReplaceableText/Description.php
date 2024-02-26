<?php

declare(strict_types=1);

namespace olml89\CoverLetter\ReplaceableText;

final readonly class Description extends ReplaceableText
{
    private const string PLACEHOLDER = '[_description_]';

    public function getPlaceholder(): string
    {
        return self::PLACEHOLDER;
    }
}
