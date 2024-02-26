<?php

declare(strict_types=1);

namespace olml89\CoverLetter\Utils;

use olml89\CoverLetter\ErrorHandling\Exceptions\InputReadingException;
use olml89\CoverLetter\ErrorHandling\Exceptions\OutputCreationException;
use olml89\CoverLetter\ErrorHandling\Exceptions\ValidationException;
use Throwable;

final readonly class Command
{
    /**
     * https://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
     */
    private const int EX_OK = 0;
    private const int EX_USAGE = 64;
    private const int EX_NOINPUT = 66;
    private const int EX_SOFTWARE = 70;
    private const int EX_CANTCREAT = 73;

    public function __construct(
        public int $status,
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
            self::EX_OK,
            self::formatMessage(
                'Cover letter for file written correctly at %s',
                $path,
            )
        );
    }

    public static function usage(ValidationException $e): self
    {
        return new self(
            self::EX_USAGE,
            self::formatException($e),
        );
    }

    public static function noinput(InputReadingException $e): self
    {
        return new self(
            self::EX_NOINPUT,
            self::formatException($e),
        );
    }

    public static function software(Throwable $e): self
    {
        return new self(
            self::EX_SOFTWARE,
            self::formatException($e),
        );
    }

    public static function cantCreate(OutputCreationException $e): self
    {
        return new self(
            self::EX_CANTCREAT,
            self::formatException($e),
        );
    }
}
