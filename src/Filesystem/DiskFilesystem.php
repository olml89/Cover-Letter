<?php

declare(strict_types=1);

namespace olml89\CoverLetter\Filesystem;

use olml89\CoverLetter\ErrorHandling\Exceptions\InputReadingException;
use olml89\CoverLetter\ErrorHandling\Exceptions\OutputCreationException;
use olml89\CoverLetter\ErrorHandling\Exceptions\ValidationException;
use Throwable;

final class DiskFilesystem implements Filesystem
{
    /**
     * @throws InputReadingException
     */
    public function getDirectory(string $path): Directory
    {
        if (!is_dir($path) || !is_readable($path)) {
            throw InputReadingException::dir($path);
        }

        return new Directory($this, $path);
    }

    /**
     * @throws InputReadingException
     * @throws OutputCreationException
     */
    public function createDirectory(string $path): Directory
    {
        if (is_dir($path)) {
            if (is_readable($path)) {
                return new Directory($this, $path);
            }

            throw InputReadingException::dir($path);
        }

        if (!mkdir($path)) {
            throw OutputCreationException::dir($path);
        }

        return new Directory($this, $path);
    }

    /**
     * @throws InputReadingException
     * @throws ValidationException
     */
    public function getTemplateFile(string $path): TemplateFile
    {
        if (!file_exists($path) || !is_readable($path)) {
            throw InputReadingException::file($path);
        }

        try {
            return new TemplateFile(file_get_contents($path));
        }
        catch (ValidationException $e) {
            throw $e;
        }
        catch (Throwable) {
            throw InputReadingException::fileContent($path);
        }
    }

    /**
     * @throws OutputCreationException
     */
    public function saveWritableFile(WritableFile $writableFile): void
    {
        try {
            file_put_contents($writableFile->path, $writableFile->content);
        }
        catch (Throwable) {
            throw OutputCreationException::file($writableFile->path);
        }
    }
}
