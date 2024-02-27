<?php

declare(strict_types=1);

namespace olml89\CoverLetter;

use olml89\CoverLetter\ErrorHandling\Exceptions\InputReadingException;
use olml89\CoverLetter\Filesystem\Directory;
use olml89\CoverLetter\Filesystem\Filesystem;
use olml89\CoverLetter\Filesystem\TemplateFile;
use olml89\CoverLetter\Utils\RequiresArrayConfigurationFile;

final readonly class Configuration
{
    use RequiresArrayConfigurationFile;

    private const string PATH = __DIR__ . '/../config/config.php';

    public function __construct(
        public Directory $coverLettersDirectory,
        public TemplateFile $coverLetterTemplate,
        public string $coverLetterFileName,
    ) {}

    /**
     * @throws InputReadingException
     */
    public static function fromPath(Filesystem $filesystem): self
    {
        $data = self::requireArrayConfigurationFile(self::PATH);

        return new self(
            coverLettersDirectory: $filesystem->getDirectory($data['cover_letters_directory_path']),
            coverLetterTemplate: $filesystem->getTemplateFile($data['cover_letter_template_file_path']),
            coverLetterFileName: $data['cover_letter_file_name'],
        );
    }
}
