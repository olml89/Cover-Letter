<?php

declare(strict_types=1);

namespace olml89\CoverLetter;

use InvalidArgumentException;
use olml89\CoverLetter\ErrorHandling\Exceptions\OutputCreationException;
use olml89\CoverLetter\PDFCreator\Metadata;
use olml89\CoverLetter\PDFCreator\PDFCreator;
use olml89\CoverLetter\ReplaceableText\Company;
use olml89\CoverLetter\ReplaceableText\Position;

final readonly class CoverLetterCreator
{
    public function __construct(
        private PDFCreator $pdfCreator,
        private Configuration $configuration,
        private Metadata $metadata,
    ) {}

    /**
     * @throws OutputCreationException
     */
    public function create(Position $position, Company $company): string
    {
        $coverLetterFilePath = $this
            ->configuration
            ->coverLettersDirectory
            ->createSubdirectory($company)
            ->getPath($this->configuration->coverLetterFileName);

        $coverLetterTemplate = $this
            ->configuration
            ->coverLetterTemplate
            ->replace($position)
            ->replace($company);

        $this
            ->pdfCreator
            ->create(
                path: $coverLetterFilePath,
                metadata: $this->metadata,
                template: $coverLetterTemplate,
            );

        return $coverLetterFilePath;
    }
}

