<?php

namespace olml89\CoverLetter\PDFCreator;

use olml89\CoverLetter\ErrorHandling\Exceptions\OutputCreationException;
use olml89\CoverLetter\Utils\WritableFile;
use olml89\CoverLetter\Utils\TemplateFile;

interface PDFCreator
{
    /**
     * @throws OutputCreationException
     */
    public function create(string $path, Metadata $metadata, TemplateFile $template): WritableFile;
}
