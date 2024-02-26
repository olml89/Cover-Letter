<?php

declare(strict_types=1);

use olml89\CoverLetter\Utils\Env;

return [
    'cover_letters_directory' => Env::get('COVER_LETTERS_DIRECTORY'),
    'cover_letter_template_file_path' => Env::get('COVER_LETTER_TEMPLATE_FILE'),
    'cover_letter_file_name' => Env::get('COVER_LETTER_FILE'),
];

