<?php

declare(strict_types=1);

namespace olml89\CoverLetter;

use olml89\CoverLetter\ReplaceableText\Company;
use olml89\CoverLetter\ReplaceableText\Position;
use olml89\CoverLetter\Utils\Result;

final readonly class CoverLetter
{
    public function __construct(
        private CoverLetterCreator $coverLetterCreator,
    ) {}

    public function create(Position $position, Company $company): Result
    {
        $coverLetterFilePath = $this->coverLetterCreator->create($position, $company);

        return Result::success($coverLetterFilePath);
    }
}
