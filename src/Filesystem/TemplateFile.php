<?php

declare(strict_types=1);

namespace olml89\CoverLetter\Filesystem;

use olml89\CoverLetter\ErrorHandling\Exceptions\ValidationException;
use olml89\CoverLetter\ReplaceableText\IsReplaceable;

final readonly class TemplateFile
{
    /**
     * @throws ValidationException
     */
    public function __construct(
        public string $content,
    ) {
        self::assertIsValidHtml($this->content);
    }

    /**
     * @throws ValidationException
     */
    private function assertIsValidHtml(string $content): void
    {
        if (!$this->isValidHtml($content)) {
            throw new ValidationException('The content of the Template file must be valid HTML');
        }
    }

    private function isValidHtml(string $content): bool
    {
        libxml_use_internal_errors(true);
        libxml_clear_errors();
        simplexml_load_string($content);

        return count(libxml_get_errors()) === 0;
    }

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

    public function equals(TemplateFile $templateFile): bool
    {
        return $this->content === $templateFile->content;
    }
}
