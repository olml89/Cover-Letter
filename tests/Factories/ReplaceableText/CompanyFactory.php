<?php

declare(strict_types=1);

namespace Tests\Factories\ReplaceableText;

use olml89\CoverLetter\ReplaceableText\Company;

final readonly class CompanyFactory extends Factory
{
    public function generate(?string $company = null): Company
    {
        return new Company($company ?? $this->randomStringGenerator->generate());
    }
}
