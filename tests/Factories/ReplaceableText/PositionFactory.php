<?php

declare(strict_types=1);

namespace Tests\Factories\ReplaceableText;

use olml89\CoverLetter\ReplaceableText\Position;

final readonly class PositionFactory extends Factory
{
    public function generate(?string $position = null): Position
    {
        return new Position($position ?? $this->randomStringGenerator->generate());
    }
}
