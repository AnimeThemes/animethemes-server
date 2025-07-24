<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Audio;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\Wiki\Audio;

class AudioLinkField extends StringField
{
    public function __construct()
    {
        parent::__construct(Audio::ATTRIBUTE_LINK, nullable: false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The URL to stream the file from storage';
    }
}
