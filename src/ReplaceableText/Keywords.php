<?php

declare(strict_types=1);

namespace olml89\CoverLetter\ReplaceableText;

final readonly class Keywords extends ReplaceableText
{
    private const string PLACEHOLDER = '[_keywords_]';

    public function getPlaceholder(): string
    {
        return self::PLACEHOLDER;
    }
}
