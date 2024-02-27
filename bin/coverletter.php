<?php

declare(strict_types=1);

use olml89\CoverLetter\Application;
use olml89\CoverLetter\CoverLetter;
use olml89\CoverLetter\ReplaceableText\Company;
use olml89\CoverLetter\ReplaceableText\Position;

require './vendor/autoload.php';

$coverLetter = Application::bootstrap()->get(CoverLetter::class);

$result = $coverLetter->create(
    Position::fromInput($argv[1] ?? null),
    Company::fromInput($argv[2] ?? null)
);

echo $result->message;
exit($result->status);
