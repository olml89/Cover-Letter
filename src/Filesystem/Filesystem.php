<?php

declare(strict_types=1);

namespace olml89\CoverLetter\Filesystem;

use olml89\CoverLetter\ErrorHandling\Exceptions\InputReadingException;
use olml89\CoverLetter\ErrorHandling\Exceptions\OutputCreationException;
use olml89\CoverLetter\ErrorHandling\Exceptions\ValidationException;

interface Filesystem
{
    /**
     * @throws InputReadingException
     */
    public function require(string $path): array;

    /**
     * @throws InputReadingException
     */
    public function getDirectory(string $path): Directory;

    /**
     * @throws OutputCreationException
     */
    public function createDirectory(string $path): Directory;

    /**
     * @throws InputReadingException
     * @throws ValidationException
     */
    public function getTemplateFile(string $path): TemplateFile;

    /**
     * @throws OutputCreationException
     */
    public function saveWritableFile(WritableFile $writableFile): void;
}
