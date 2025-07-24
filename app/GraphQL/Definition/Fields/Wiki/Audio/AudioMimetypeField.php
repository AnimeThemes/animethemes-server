<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Audio;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\Wiki\Audio;

class AudioMimetypeField extends StringField
{
    public function __construct()
    {
        parent::__construct(Audio::ATTRIBUTE_MIMETYPE, nullable: false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The media type of the file in storage';
    }
}
