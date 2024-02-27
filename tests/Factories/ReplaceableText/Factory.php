<?php

declare(strict_types=1);

namespace Tests\Factories\ReplaceableText;

use Tests\Factories\RandomStringGenerator;

abstract readonly class Factory
{
    public function __construct(
        protected RandomStringGenerator $randomStringGenerator,
    ) {}
}
