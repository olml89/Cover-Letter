<?php

declare(strict_types=1);

use olml89\CoverLetter\Application;
use olml89\CoverLetter\IO\Input;

require __DIR__ . '/../vendor/autoload.php';

$application = Application::bootstrap();
$input = Input::read($argv, 'position', 'company');

$application->execute($input);
