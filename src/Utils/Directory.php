<?php

declare(strict_types=1);

namespace olml89\CoverLetter\Utils;

use olml89\CoverLetter\ErrorHandling\Exceptions\InputReadingException;
use olml89\CoverLetter\ErrorHandling\Exceptions\OutputCreationException;
use Stringable;

final readonly class Directory
{
    public function __construct(
        private string $path,
    ) {}

    /**
     * @throws InputReadingException
     */
    public static function fromPath(string $path): self
    {
        self::assertIsReadable($path);

        return new self($path);
    }

    /**
     * @throws InputReadingException
     */
    private static function assertIsReadable(string $path): void
    {
        if (!is_dir($path) || !is_readable($path)) {
            throw InputReadingException::dir($path);
        }
    }

    private static function checkIsReadable(string $path): bool
    {
        try {
            self::assertIsReadable($path);

            return true;
        } catch (InputReadingException) {
            return false;
        }
    }

    /**
     * @throws OutputCreationException
     */
    public function createSubdirectory(string|Stringable $subDirectoryName): self
    {
        $subDirectoryPath = $this->path . '/' . $subDirectoryName;

        if (!self::checkIsReadable($subDirectoryPath) && !mkdir($subDirectoryPath)) {
            throw OutputCreationException::dir($subDirectoryPath);
        }

        return new self($subDirectoryPath);
    }

    public function getPath(?string $filePath = null): string
    {
        return is_null($filePath) ? $this->path : $this->path . '/' . $filePath;
    }
}
