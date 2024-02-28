<?php

declare(strict_types=1);

namespace olml89\CoverLetter;

use olml89\CoverLetter\IO\Input;
use olml89\CoverLetter\IO\Result;
use olml89\CoverLetter\ReplaceableText\Company;
use olml89\CoverLetter\ReplaceableText\Position;

final readonly class CreateCoverLetter
{
    public function __construct(
        private CoverLetterCreator $coverLetterCreator,
    ) {}

    public function create(Input $input): Result
    {
        $coverLetterFilePath = $this->coverLetterCreator->create(
            position: new Position($input->get('position')),
            company: new Company($input->get('company')),
        );

        return Result::success($coverLetterFilePath);
    }
}
