<?php

declare(strict_types=1);

use olml89\CoverLetter\Utils\Env;

return [
    'creation_date' => Env::get('CREATION_DATE'),
    'creator' => Env::get('CREATOR'),
    'keywords' => Env::get('KEYWORDS'),
    'mod_date' => Env::get('MOD_DATE'),
    'producer' => Env::get('PRODUCER'),
    'description' => Env::get('DESCRIPTION'),
];

