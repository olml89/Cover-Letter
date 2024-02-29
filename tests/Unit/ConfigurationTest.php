<?php

declare(strict_types=1);

namespace Tests\Unit;

use Mockery;
use olml89\CoverLetter\Configuration;
use olml89\CoverLetter\Filesystem\Directory;
use olml89\CoverLetter\Filesystem\Filesystem;
use ReflectionClass;
use Tests\Factories\Filesystem\TemplateFileFactory;
use Tests\Factories\RandomStringGenerator;
use Tests\TestCase;

final class ConfigurationTest extends TestCase
{
    private readonly RandomStringGenerator $randomStringGenerator;
    private readonly TemplateFileFactory $templateFileFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->randomStringGenerator = $this->container->get(RandomStringGenerator::class);
        $this->templateFileFactory = $this->container->get(TemplateFileFactory::class);
    }

    public function testItLoadsConfigurationFromConfigFile(): void
    {
        $configFilePath = realpath((new ReflectionClass(Configuration::class))->getConstant('PATH'));

        $config = [
            'cover_letters_directory_path' => $this->randomStringGenerator->generate(),
            'cover_letter_template_file_path' => $this->randomStringGenerator->generate(),
            'cover_letter_file_name' => $this->randomStringGenerator->generate(),
        ];

        $template = $this->templateFileFactory->generate();

        $filesystem = $this->getInstance(
            Filesystem::class,
            Mockery::mock(
                Filesystem::class,
                function (Mockery\MockInterface $mock) use ($configFilePath, $config, $template): void {
                    $mock
                        ->shouldReceive('require')
                        ->once()
                        ->with($configFilePath)
                        ->andReturn($config);
                    $mock
                        ->shouldReceive('getDirectory')
                        ->once()
                        ->with($config['cover_letters_directory_path'])
                        ->andReturn(
                            new Directory(
                                $this->container->get(Filesystem::class),
                                $config['cover_letters_directory_path']
                            )
                        );
                    $mock
                        ->shouldReceive('getTemplateFile')
                        ->once()
                        ->with($config['cover_letter_template_file_path'])
                        ->andReturn($template);
                }
            )
        );

        $configuration = Configuration::fromPath($filesystem);

        $this->assertEquals(
            $config['cover_letters_directory_path'],
            $configuration->coverLettersDirectory->getPath()
        );
        $this->assertEquals(
            $config['cover_letter_file_name'],
            $configuration->coverLetterFileName
        );
        $this->assertTrue(
            $configuration->coverLetterTemplate->equals($template)
        );
    }
}
