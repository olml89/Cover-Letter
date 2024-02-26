<?php

declare(strict_types=1);

use olml89\CoverLetter\Utils\Env;

return [
    'creationDate' => Env::get('CREATION_DATE'),
    'creator' => Env::get('CREATOR'),
    'keywords' => Env::get('KEYWORDS'),
    'modDate' => Env::get('MOD_DATE'),
    'producer' => Env::get('PRODUCER'),
    'description' => Env::get('DESCRIPTION'),
];

