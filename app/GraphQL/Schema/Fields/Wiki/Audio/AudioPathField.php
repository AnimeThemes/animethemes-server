<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Wiki\Audio;

use App\GraphQL\Schema\Fields\StringField;
use App\Models\Wiki\Audio;

class AudioPathField extends StringField
{
    public function __construct()
    {
        parent::__construct(Audio::ATTRIBUTE_PATH, nullable: false);
    }

    public function description(): string
    {
        return 'The path of the file in storage';
    }
}
