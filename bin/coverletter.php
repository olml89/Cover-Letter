<?php

declare(strict_types=1);

use olml89\CoverLetter\CoverLetter;

require_once './vendor/autoload.php';

$result = CoverLetter::bootstrap()->create($argv);

echo $result->message;
exit($result->status);
