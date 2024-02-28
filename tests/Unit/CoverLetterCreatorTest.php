<?php

declare(strict_types=1);

namespace Tests\Unit;

use Mockery;
use olml89\CoverLetter\Configuration;
use olml89\CoverLetter\CoverLetterCreator;
use olml89\CoverLetter\Filesystem\Directory;
use olml89\CoverLetter\Filesystem\Filesystem;
use olml89\CoverLetter\Filesystem\TemplateFile;
use olml89\CoverLetter\Filesystem\WritableFile;
use olml89\CoverLetter\PDFCreator\Metadata;
use olml89\CoverLetter\PDFCreator\PDFCreator;
use Tests\Factories\Filesystem\TemplateFileFactory;
use Tests\Factories\RandomStringGenerator;
use Tests\Factories\ReplaceableText\CompanyFactory;
use Tests\Factories\ReplaceableText\PositionFactory;
use Tests\TestCase;

final class CoverLetterCreatorTest extends TestCase
{
    private readonly RandomStringGenerator $randomStringGenerator;
    private readonly TemplateFileFactory $templateFileFactory;
    private readonly PositionFactory $positionFactory;
    private readonly CompanyFactory $companyFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->randomStringGenerator = $this->container->get(RandomStringGenerator::class);
        $this->templateFileFactory = $this->container->get(TemplateFileFactory::class);
        $this->positionFactory = $this->container->get(PositionFactory::class);
        $this->companyFactory = $this->container->get(CompanyFactory::class);
    }

    public function testItReturnsCoverLetterFilePath(): void
    {
        $position = $this->positionFactory->generate();
        $company = $this->companyFactory->generate();

        $coverLettersDirectoryPath = $this->randomStringGenerator->generate();

        $companyCoverLetterSubdirectoryPath = sprintf(
            '%s/%s',
            $coverLettersDirectoryPath,
            $company,
        );

        $configuration = new Configuration(
            coverLettersDirectory: new Directory(
                filesystem: $this->getInstance(
                    Filesystem::class,
                    Mockery::mock(
                        Filesystem::class,
                        function (Mockery\MockInterface $mock) use ($companyCoverLetterSubdirectoryPath): void {
                            $mock
                                ->shouldReceive('createDirectory')
                                ->once()
                                ->with($companyCoverLetterSubdirectoryPath)
                                ->andReturn(
                                    new Directory(
                                        filesystem: $this->container->get(Filesystem::class),
                                        path: $companyCoverLetterSubdirectoryPath,
                                    )
                                );
                        }
                    )
                ),
                path: $coverLettersDirectoryPath,
            ),
            coverLetterTemplate: $this->templateFileFactory->generate(),
            coverLetterFileName: $this->randomStringGenerator->generate(),
        );

        $expectedCoverLetterFilePath = sprintf(
            '%s/%s',
            $companyCoverLetterSubdirectoryPath,
            $configuration->coverLetterFileName,
        );

        $this->container->set(
            Configuration::class,
            $configuration
        );

        $this->container->set(
            PDFCreator::class,
            Mockery::mock(
                PDFCreator::class,
                function (
                    Mockery\MockInterface $mock,
                ) use (
                    $expectedCoverLetterFilePath,
                    $configuration,
                    $position,
                    $company,
                ): void {
                    $mock
                        ->shouldReceive('create')
                        ->once()
                        ->withArgs(
                            function (
                                string $coverLetterFilePath,
                                Metadata $systemMetadata,
                                TemplateFile $processedTemplate,
                            ) use (
                                $expectedCoverLetterFilePath,
                                $configuration,
                                $position,
                                $company,
                            ): bool {
                                return $expectedCoverLetterFilePath === $coverLetterFilePath
                                    && $systemMetadata === $this->container->get(Metadata::class)
                                    && $processedTemplate->equals(
                                        $configuration
                                            ->coverLetterTemplate
                                            ->replace($position)
                                            ->replace($company)
                                    );
                            }
                        )
                        ->andReturn(
                            new WritableFile(
                                path: $expectedCoverLetterFilePath,
                                content: $this->randomStringGenerator->generate()
                            )
                        );
                }
            )
        );

        $coverLetterFilePath = $this->container->get(CoverLetterCreator::class)->create(
            $position,
            $company
        );

        $this->assertEquals(
            $expectedCoverLetterFilePath,
            $coverLetterFilePath
        );
    }
}
