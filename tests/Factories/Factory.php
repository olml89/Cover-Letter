<?php

declare(strict_types=1);

namespace Tests\Factories;

abstract readonly class Factory
{
    public function __construct(
        protected RandomStringGenerator $randomStringGenerator,
    ) {}
}
