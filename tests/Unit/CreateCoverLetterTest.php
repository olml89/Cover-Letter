<?php

declare(strict_types=1);

namespace Tests\Unit;

use Mockery;
use olml89\CoverLetter\CoverLetterCreator;
use olml89\CoverLetter\CreateCoverLetter;
use olml89\CoverLetter\Filesystem\Directory;
use olml89\CoverLetter\Filesystem\Filesystem;
use olml89\CoverLetter\Filesystem\TemplateFile;
use olml89\CoverLetter\Utils\Result;
use Tests\Factories\RandomStringGenerator;
use Tests\Factories\ReplaceableText\CompanyFactory;
use Tests\Factories\ReplaceableText\PositionFactory;
use Tests\TestCase;

final class CreateCoverLetterTest extends TestCase
{
    private readonly RandomStringGenerator $randomStringGenerator;
    private readonly PositionFactory $positionFactory;
    private readonly CompanyFactory $companyFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->randomStringGenerator = $this->container->get(RandomStringGenerator::class);

        $config = require __DIR__ . '/../../config/config.php';

        $this->container->set(
            Filesystem::class,
            Mockery::mock(
                Filesystem::class,
                function (Mockery\MockInterface $mock) use ($config): void {
                    $mock
                        ->shouldReceive('getDirectory')
                        ->once()
                        ->with($config['cover_letters_directory_path'])
                        ->andReturn(new Directory(
                            $this->container->get(Filesystem::class),
                            $config['cover_letters_directory_path']
                        ));
                    $mock
                        ->shouldReceive('getTemplateFile')
                        ->once()
                        ->with($config['cover_letter_template_file_path'])
                        ->andReturn(new TemplateFile('<html></html>'));
                }
            )
        );

        $this->positionFactory = $this->container->get(PositionFactory::class);
        $this->companyFactory = $this->container->get(CompanyFactory::class);
    }

    public function testItCreatesSuccessCommandIfInputIsCorrect(): void
    {
        $position = $this->positionFactory->generate();
        $company = $this->companyFactory->generate();
        $expectedCoverLetterFilePath = $this->randomStringGenerator->generate();
        $expectedResult = Result::success($expectedCoverLetterFilePath);

        $this->container->set(
            CoverLetterCreator::class,
            Mockery::mock(
                $this->container->get(CoverLetterCreator::class),
                function (Mockery\MockInterface $mock) use ($position, $company, $expectedCoverLetterFilePath): void {
                    $mock
                        ->shouldReceive('create')
                        ->once()
                        ->with($position, $company)
                        ->andReturn($expectedCoverLetterFilePath);
                }
            )->makePartial()
        );

        $result = $this->container->get(CreateCoverLetter::class)->create($position, $company);

        $this->assertEquals($expectedResult, $result);
    }
}
