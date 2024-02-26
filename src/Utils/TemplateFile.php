<?php

namespace olml89\CoverLetter\Utils;

use olml89\CoverLetter\ErrorHandling\Exceptions\InputReadingException;
use olml89\CoverLetter\ReplaceableText\IsReplaceable;
use RuntimeException;

final readonly class TemplateFile
{
    private function __construct(
        private string $content,
    ) {}

    public static function fromContent(string $content): self
    {
        return new self($content);
    }

    /**
     * @throws InputReadingException
     * @throws RuntimeException
     */
    public static function fromPath(string $path): self
    {
        self::assertIsReadable($path);

        if (($content = file_get_contents($path)) === false) {
            throw new RuntimeException(sprintf(
                'Error getting content from \'%s\'',
                $path,
            ));
        }

        return self::fromContent($content);
    }

    /**
     * @throws InputReadingException
     */
    private static function assertIsReadable(string $path): void
    {
        if (!file_exists($path) || !is_readable($path)) {
            throw InputReadingException::file($path);
        }
    }

    public function replace(IsReplaceable $replaceableText): self
    {
        return self::fromContent(
            str_replace(
                search: $replaceableText->getPlaceholder(),
                replace: $replaceableText->getText(),
                subject: $this->content,
            )
        );
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
