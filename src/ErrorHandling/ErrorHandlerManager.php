<?php

declare(strict_types=1);

namespace olml89\CoverLetter\ErrorHandling;

use ErrorException;
use JetBrains\PhpStorm\NoReturn;
use Throwable;

final readonly class ErrorHandlerManager
{
    public function __construct(
        private ErrorHandler $errorHandler,
    ) {
        error_reporting(-1);

        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);
    }

    public function shutdown(): void
    {
        restore_error_handler();
        restore_exception_handler();
    }

    /**
     * @throws ErrorException
     */
    private function handleError(int $level, string $message, string $file = '', int $line = 0, array $context = []): void
    {
        throw new ErrorException(
            message: $message,
            code: 0,
            severity: $level,
            filename: $file,
            line: $line,
        );
    }

    #[NoReturn]
    private function handleException(Throwable $e): void
    {
        $this->errorHandler->handle($e);
    }

    /**
     * @throws ErrorException
     */
    private function handleShutdown(): void
    {
        $fatalErrorTypes = [E_COMPILE_ERROR, E_CORE_ERROR, E_ERROR, E_PARSE];

        if (!is_null($error = error_get_last()) && in_array($error['type'], $fatalErrorTypes)) {
            self::handleError(
                level: $error['type'],
                message: $error['message'],
                file: $error['file'],
                line: $error['line'],
            );
        }
    }
}