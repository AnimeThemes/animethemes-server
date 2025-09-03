<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Wiki\Audio;

use App\GraphQL\Schema\Fields\StringField;
use App\Models\Wiki\Audio;

class AudioFilenameField extends StringField
{
    public function __construct()
    {
        parent::__construct(Audio::ATTRIBUTE_FILENAME, nullable: false);
    }

    public function description(): string
    {
        return 'The filename of the file in storage';
    }
}
