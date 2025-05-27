<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Audio;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\Wiki\Audio;

/**
 * Class AudioFilenameField.
 */
class AudioFilenameField extends StringField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Audio::ATTRIBUTE_FILENAME, nullable: false);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The filename of the file in storage';
    }
}
