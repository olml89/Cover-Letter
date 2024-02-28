<?php

declare(strict_types=1);

namespace Tests\Unit\Filesystem;

use olml89\CoverLetter\ErrorHandling\Exceptions\ValidationException;
use olml89\CoverLetter\Filesystem\TemplateFile;
use olml89\CoverLetter\ReplaceableText\ReplaceableText;
use Tests\Factories\Filesystem\TemplateFileFactory;
use Tests\Factories\RandomStringGenerator;
use Tests\TestCase;

final class TemplateFileTest extends TestCase
{
    private readonly RandomStringGenerator $randomStringGenerator;
    private readonly TemplateFileFactory $templateFileFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->randomStringGenerator = $this->container->get(RandomStringGenerator::class);
        $this->templateFileFactory = $this->container->get(TemplateFileFactory::class);
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
        $content = $this->templateFileFactory->generate()->content;

        $template = new TemplateFile($content);

        $this->assertInstanceOf(TemplateFile::class, $template);
        $this->assertEquals($content, $template->content);
    }

    public function testItReturnsANewInstanceWithReplacedText(): void
    {
        $replaceableText = new readonly class(
            $this->randomStringGenerator->generate(),
            $this->randomStringGenerator->generate()
        ) extends ReplaceableText {
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

        $template = $this
            ->templateFileFactory
            ->generate($replaceableText->getPlaceholder());

        $this->assertEquals(
            str_replace(
                $replaceableText->getPlaceholder(),
                $replaceableText->getText(),
                $template->content,
            ),
            $template->replace($replaceableText)->content
        );
    }

    public function testItComparesTemplates(): void
    {
        $placeholder = $this->randomStringGenerator->generate();
        $template = $this->templateFileFactory->generate($placeholder);
        $equalTemplate = $this->templateFileFactory->generate($placeholder);
        $notEqualTemplate = $this->templateFileFactory->generate($this->randomStringGenerator->generate());

        $this->assertTrue($template->equals($equalTemplate));
        $this->assertFalse($template->equals($notEqualTemplate));
    }
}
