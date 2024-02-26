<?php

declare(strict_types=1);

namespace olml89\CoverLetter\ErrorHandling;

use JetBrains\PhpStorm\NoReturn;
use olml89\CoverLetter\ErrorHandling\Exceptions\InputReadingException;
use olml89\CoverLetter\ErrorHandling\Exceptions\OutputCreationException;
use olml89\CoverLetter\ErrorHandling\Exceptions\ValidationException;
use olml89\CoverLetter\Utils\Command;
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

    private function mapExceptionToCommand(Throwable $e): Command
    {
        return match ($e::class) {
            ValidationException::class => Command::usage($e),
            InputReadingException::class => Command::noinput($e),
            OutputCreationException::class => Command::cantCreate($e),
            default => Command::software($e),
        };
    }
}
