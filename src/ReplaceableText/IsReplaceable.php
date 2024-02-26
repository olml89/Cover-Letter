<?php

declare(strict_types=1);

namespace olml89\CoverLetter\ReplaceableText;

interface IsReplaceable
{
    public function getPlaceholder(): string;
    public function getText(): string;
}
