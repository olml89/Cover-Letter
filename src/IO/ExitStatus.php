<?php

declare(strict_types=1);

namespace olml89\CoverLetter\IO;

enum ExitStatus: int
{
    /**
     * https://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
     */
    case EX_OK = 0;
    case EX_USAGE = 64;
    case EX_NOINPUT = 66;
    case EX_SOFTWARE = 70;
    case EX_CANTCREAT = 73;
}
