<?php

declare(strict_types=1);

namespace olml89\CoverLetter\ErrorHandling;

use JetBrains\PhpStorm\NoReturn;
use olml89\CoverLetter\ErrorHandling\Exceptions\InputReadingException;
use olml89\CoverLetter\ErrorHandling\Exceptions\OutputCreationException;
use olml89\CoverLetter\ErrorHandling\Exceptions\ValidationException;
use olml89\CoverLetter\IO\Result;
use Throwable;

final class ErrorHandler
{
    #[NoReturn]
    public function handle(Throwable $e): void
    {
        $errorCommand = $this->mapExceptionToCommand($e);

        echo $errorCommand->message;
        exit($errorCommand->status);
    }

    private function mapExceptionToCommand(Throwable $e): Result
    {
        return match ($e::class) {
            ValidationException::class => Result::usage($e),
            InputReadingException::class => Result::noinput($e),
            OutputCreationException::class => Result::cantCreate($e),
            default => Result::software($e),
        };
    }
}
