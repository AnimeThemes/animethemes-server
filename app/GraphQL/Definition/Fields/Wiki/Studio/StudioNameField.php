<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Studio;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\Wiki\Studio;

/**
 * Class StudioNameField.
 */
class StudioNameField extends StringField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Studio::ATTRIBUTE_NAME, nullable: false);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The primary title of the Studio';
    }
}
