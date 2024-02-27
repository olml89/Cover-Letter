<?php

namespace olml89\CoverLetter\Filesystem;

use olml89\CoverLetter\ErrorHandling\Exceptions\InputReadingException;
use olml89\CoverLetter\ReplaceableText\IsReplaceable;
use RuntimeException;

final readonly class TemplateFile
{
    public function __construct(
        public string $content,
    ) {}

    public function replace(IsReplaceable $replaceableText): self
    {
        return new self(
            str_replace(
                search: $replaceableText->getPlaceholder(),
                replace: $replaceableText->getText(),
                subject: $this->content,
            )
        );
    }
}
