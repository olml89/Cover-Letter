<?php

declare(strict_types=1);

namespace Tests\Unit\ErrorHandling;

use Exception;
use JetBrains\PhpStorm\NoReturn;
use Mockery;
use olml89\CoverLetter\ErrorHandling\ErrorHandler;
use olml89\CoverLetter\ErrorHandling\ErrorHandlerManager;
use olml89\CoverLetter\ErrorHandling\Exceptions\InputReadingException;
use olml89\CoverLetter\ErrorHandling\Exceptions\OutputCreationException;
use olml89\CoverLetter\ErrorHandling\Exceptions\ValidationException;
use olml89\CoverLetter\IO\ExitStatus;
use olml89\CoverLetter\IO\Output;
use olml89\CoverLetter\IO\Result;
use Tests\Factories\RandomStringGenerator;
use Tests\TestCase;

final class ErrorHandlerTest extends TestCase
{
    private readonly string $errorMessage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->errorMessage = $this->container->get(RandomStringGenerator::class)->generate();
    }

    private function expectExitStatus(ExitStatus $exitStatus): void
    {
        $this->container->set(
            Output::class,
            Mockery::mock(
                $this->container->get(Output::class),
                function (Mockery\MockInterface $mock) use ($exitStatus): void {
                    $mock
                        ->shouldReceive('die')
                        ->once()
                        ->with($exitStatus)
                        ->andReturn();
                }
            )->makePartial()
        );

        $this->container->set(
            ErrorHandler::class,
            $this->container->make(ErrorHandler::class)
        );

        $this->container->set(
            ErrorHandlerManager::class,
            $this->container->make(ErrorHandlerManager::class)
        );
    }

    #[NoReturn]
    public function testItOutputsAUsageExitStatusResultOnValidationException(): void
    {
        $validationException = new ValidationException($this->errorMessage);
        $result = Result::usage($validationException);

        $this->expectOutputString($result->message);
        $this->expectExitStatus($result->status);

        $this
            ->container
            ->get(ErrorHandler::class)
            ->handle($validationException);
    }

    #[NoReturn]
    public function testItOutputsANoInputExitStatusResultOnInputReadingException(): void
    {
        $inputReadingException = new InputReadingException($this->errorMessage);
        $result = Result::noinput($inputReadingException);

        $this->expectOutputString($result->message);
        $this->expectExitStatus($result->status);

        $this
            ->container
            ->get(ErrorHandler::class)
            ->handle($inputReadingException);
    }

    #[NoReturn]
    public function testItOutputsACantCreateExitStatusResultOnOutputCreationException(): void
    {
        $outputCreationException = new OutputCreationException($this->errorMessage);
        $result = Result::cantCreate($outputCreationException);

        $this->expectOutputString($result->message);
        $this->expectExitStatus($result->status);

        $this
            ->container
            ->get(ErrorHandler::class)
            ->handle($outputCreationException);
    }

    #[NoReturn]
    public function testItOutputsASoftwareExitStatusResultOnGenericException(): void
    {
        $exception = new Exception($this->errorMessage);
        $result = Result::software($exception);

        $this->expectOutputString($result->message);
        $this->expectExitStatus($result->status);

        $this
            ->container
            ->get(ErrorHandler::class)
            ->handle($exception);
    }
}
