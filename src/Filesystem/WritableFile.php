<?php

declare(strict_types=1);

namespace olml89\CoverLetter\Filesystem;

final readonly class WritableFile
{
    public function __construct(
        public string $path,
        public string $content,
    ) {}
}
