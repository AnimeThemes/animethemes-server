<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Audio;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\Wiki\Audio;

class AudioPathField extends StringField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Audio::ATTRIBUTE_PATH, nullable: false);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The path of the file in storage';
    }
}
