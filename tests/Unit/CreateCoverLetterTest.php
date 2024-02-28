<?php

declare(strict_types=1);

namespace Tests\Unit;

use Mockery;
use olml89\CoverLetter\CoverLetterCreator;
use olml89\CoverLetter\CreateCoverLetter;
use olml89\CoverLetter\IO\Input;
use olml89\CoverLetter\IO\Result;
use olml89\CoverLetter\ReplaceableText\Company;
use olml89\CoverLetter\ReplaceableText\Position;
use Tests\Factories\RandomStringGenerator;
use Tests\TestCase;

final class CreateCoverLetterTest extends TestCase
{
    private readonly RandomStringGenerator $randomStringGenerator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->randomStringGenerator = $this->container->get(RandomStringGenerator::class);
    }

    public function testItCreatesSuccessCommandIfInputIsCorrect(): void
    {
        $positionText = $this->randomStringGenerator->generate();
        $companyText = $this->randomStringGenerator->generate();
        $expectedCoverLetterFilePath = $this->randomStringGenerator->generate();
        $expectedResult = Result::success($expectedCoverLetterFilePath);

        $this->container->set(
            CoverLetterCreator::class,
            Mockery::mock(
                CoverLetterCreator::class,
                function (
                    Mockery\MockInterface $mock
                ) use (
                    $positionText,
                    $companyText,
                    $expectedCoverLetterFilePath,
                ): void {
                    $mock
                        ->shouldReceive('create')
                        ->once()
                        ->withArgs(
                            function (Position $position, Company $company) use ($positionText, $companyText): bool {
                                return $position->getText() === $positionText
                                    && $company->getText() === $companyText;
                            }
                        )
                        ->andReturn($expectedCoverLetterFilePath);
                }
            )
        );

        $result = $this->container->get(CreateCoverLetter::class)->create(
            new Input([
                'position' => $positionText,
                'company' => $companyText,
            ])
        );

        $this->assertEquals($expectedResult, $result);
    }
}
