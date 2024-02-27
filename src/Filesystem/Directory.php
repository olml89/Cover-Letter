<?php

declare(strict_types=1);

namespace olml89\CoverLetter\Filesystem;

use olml89\CoverLetter\ErrorHandling\Exceptions\OutputCreationException;
use Stringable;

final readonly class Directory
{
    public function __construct(
        private Filesystem $filesystem,
        private string $path,
    ) {}

    /**
     * @throws OutputCreationException
     */
    public function createSubdirectory(string|Stringable $subDirectoryName): self
    {
        return $this
            ->filesystem
            ->createDirectory($this->getPath($subDirectoryName));
    }

    public function getPath(string|Stringable|null $subPath = null): string
    {
        return is_null($subPath)
            ? $this->path
            : $this->path . '/' . $subPath;
    }
}
