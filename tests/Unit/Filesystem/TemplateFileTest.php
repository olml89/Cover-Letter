<?php

declare(strict_types=1);

namespace Tests\Unit\Filesystem;

use olml89\CoverLetter\ErrorHandling\Exceptions\ValidationException;
use olml89\CoverLetter\Filesystem\TemplateFile;
use olml89\CoverLetter\ReplaceableText\ReplaceableText;
use Tests\Factories\RandomStringGenerator;
use Tests\TestCase;

final class TemplateFileTest extends TestCase
{
    private readonly RandomStringGenerator $randomStringGenerator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->randomStringGenerator = $this->container->get(RandomStringGenerator::class);
    }

    public function testItDoesNotAllowInvalidHtml(): void
    {
        $content = $this->randomStringGenerator->generate();

        $this->expectExceptionObject(
            new ValidationException('The content of the Template file must be valid HTML')
        );

        new TemplateFile($content);
    }

    public function testItAllowsValidHtml(): void
    {
        $content = file_get_contents(__DIR__ . '/../../Fixtures/cover_letter_template.html');

        $template = new TemplateFile($content);

        $this->assertInstanceOf(TemplateFile::class, $template);
        $this->assertEquals($content, $template->content);
    }

    public function testItReturnsANewInstanceWithReplacedText(): void
    {
        $contentFormat = '<html><body><p>%s</p></body></html>';

        $placeholder = $this->randomStringGenerator->generate();
        $text = $this->randomStringGenerator->generate();

        $replaceableText = new readonly class($placeholder, $text) extends ReplaceableText
        {
            public function __construct(
                private string $placeholder,
                string $text,
            ) {
                parent::__construct($text);
            }

            public function getPlaceholder(): string
            {
                return $this->placeholder;
            }
        };

        $templateFile = (new TemplateFile(sprintf($contentFormat, $placeholder)))->replace($replaceableText);

        $this->assertEquals(
            sprintf($contentFormat, $text),
            $templateFile->content
        );
    }
}
