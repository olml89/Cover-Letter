<?php

declare(strict_types=1);

namespace olml89\CoverLetter;

use olml89\CoverLetter\ErrorHandling\Exceptions\InputReadingException;
use olml89\CoverLetter\Utils\Directory;
use olml89\CoverLetter\Utils\LoadableFromPath;
use olml89\CoverLetter\Utils\TemplateFile;

final readonly class Configuration
{
    use LoadableFromPath;

    public function __construct(
        public Directory $coverLettersDirectory,
        public TemplateFile $coverLetterTemplate,
        public string $coverLetterFileName,
    ) {}

    /**
     * @throws InputReadingException
     * @throws RuntimeException
     */
    public static function fromArray(array $data): static
    {
        return new self(
            coverLettersDirectory: Directory::fromPath($data['cover_letters_directory']),
            coverLetterTemplate: TemplateFile::fromPath($data['cover_letter_template_file_path']),
            coverLetterFileName: $data['cover_letter_file_name'],
        );
    }
}
