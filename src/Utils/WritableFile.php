<?php

declare(strict_types=1);

namespace olml89\CoverLetter\Utils;

use olml89\CoverLetter\ErrorHandling\Exceptions\OutputCreationException;

final readonly class WritableFile
{
    public function __construct(
        private string $path,
        private string $content,
    ) {}

    /**
     * @throws OutputCreationException
     */
    public function save(): void
    {
        if (!file_put_contents($this->path, $this->content)) {
            throw OutputCreationException::file($this->path);
        }
    }
}
