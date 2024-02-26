<?php

declare(strict_types=1);

namespace olml89\CoverLetter\ReplaceableText;

final readonly class Company extends ReplaceableText
{
    private const string PLACEHOLDER = '[_company_]';

    public function getPlaceholder(): string
    {
        return self::PLACEHOLDER;
    }
}
