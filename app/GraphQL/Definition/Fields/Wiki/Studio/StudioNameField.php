<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Studio;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\Wiki\Studio;

class StudioNameField extends StringField
{
    public function __construct()
    {
        parent::__construct(Studio::ATTRIBUTE_NAME, nullable: false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The primary title of the Studio';
    }
}
