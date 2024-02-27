<?php

declare(strict_types=1);

namespace olml89\CoverLetter\Filesystem;

use olml89\CoverLetter\ErrorHandling\Exceptions\InputReadingException;
use olml89\CoverLetter\ErrorHandling\Exceptions\OutputCreationException;

final class DiskFilesystem implements Filesystem
{
    /**
     * @throws InputReadingException
     */
    public function getDirectory(string $path): Directory
    {
        if (!$this->directoryExistsAndIsReadable($path)) {
            throw InputReadingException::dir($path);
        }

        return new Directory($this, $path);
    }

    /**
     * @throws OutputCreationException
     */
    public function createDirectory(string $path): Directory
    {
        if ($this->directoryExistsAndIsReadable($path)) {
            return new Directory($this, $path);
        }

        if (!mkdir($path)) {
            throw OutputCreationException::dir($path);
        }

        return new Directory($this, $path);
    }

    private function directoryExistsAndIsReadable(string $path): bool
    {
        return is_dir($path) && is_readable($path);
    }

    /**
     * @throws InputReadingException
     */
    public function getTemplateFile(string $path): TemplateFile
    {
        if (!file_exists($path) || !is_readable($path)) {
            throw InputReadingException::file($path);
        }

        if (($content = file_get_contents($path)) === false) {
            throw InputReadingException::fileContent($path);
        }

        return new TemplateFile($content);
    }

    /**
     * @throws OutputCreationException
     */
    public function saveWritableFile(WritableFile $writableFile): void
    {
        if (!file_put_contents($writableFile->path, $writableFile->content)) {
            throw OutputCreationException::file($writableFile->path);
        }
    }
}
