<?php

declare(strict_types=1);

namespace olml89\CoverLetter\Filesystem;

use olml89\CoverLetter\ErrorHandling\Exceptions\InputReadingException;
use olml89\CoverLetter\ErrorHandling\Exceptions\OutputCreationException;

interface Filesystem
{
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
     */
    public function getTemplateFile(string $path): TemplateFile;

    /**
     * @throws OutputCreationException
     */
    public function saveWritableFile(WritableFile $writableFile): void;
}
