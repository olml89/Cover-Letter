<?php

declare(strict_types=1);

namespace olml89\CoverLetter\IO;

use JetBrains\PhpStorm\NoReturn;

final class Output
{
    public function write(string $message): void
    {
        echo $message;
    }

    #[NoReturn]
    public function die(ExitStatus $exitStatus): void
    {
        exit($exitStatus->value);
    }
}
