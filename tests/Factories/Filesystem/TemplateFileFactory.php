<?php

declare(strict_types=1);

namespace Tests\Factories\Filesystem;

use olml89\CoverLetter\Filesystem\TemplateFile;

final class TemplateFileFactory
{
    private const string SKELETON = '<html>%s</html>';

    public function generate(?string $placeholder = null): TemplateFile
    {
        return new TemplateFile(
            sprintf(self::SKELETON, $placeholder ?? '')
        );
    }
}
