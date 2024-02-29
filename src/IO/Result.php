<?php

declare(strict_types=1);

namespace olml89\CoverLetter\IO;

use olml89\CoverLetter\ErrorHandling\Exceptions\InputReadingException;
use olml89\CoverLetter\ErrorHandling\Exceptions\OutputCreationException;
use olml89\CoverLetter\ErrorHandling\Exceptions\ValidationException;
use Throwable;

final readonly class Result
{
    public function __construct(
        public ExitStatus $status,
        public string $message,
    ) {}

    private static function formatMessage(string $message, string ...$parameters): string
    {
        $message .= "\n";

        return sprintf($message, ...$parameters);
    }

    private static function formatException(Throwable $e): string
    {
        return self::formatMessage(
            sprintf(
                '%s%s%s%s%s',
                $e::class,
                PHP_EOL,
                $e->getMessage(),
                PHP_EOL,
                $e->getTraceAsString(),
            )
        );
    }

    public static function success(string $path): self
    {
        return new self(
            ExitStatus::EX_OK,
            self::formatMessage(
                'Cover letter for file written correctly at %s',
                $path,
            )
        );
    }

    public static function usage(ValidationException $e): self
    {
        return new self(
            ExitStatus::EX_USAGE,
            self::formatException($e),
        );
    }

    public static function noinput(InputReadingException $e): self
    {
        return new self(
            ExitStatus::EX_NOINPUT,
            self::formatException($e),
        );
    }

    public static function software(Throwable $e): self
    {
        return new self(
            ExitStatus::EX_SOFTWARE,
            self::formatException($e),
        );
    }

    public static function cantCreate(OutputCreationException $e): self
    {
        return new self(
            ExitStatus::EX_CANTCREAT,
            self::formatException($e),
        );
    }
}
