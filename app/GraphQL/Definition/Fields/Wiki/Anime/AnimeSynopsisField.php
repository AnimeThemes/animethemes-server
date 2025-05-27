<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Anime;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\Wiki\Anime;

/**
 * Class AnimeSynopsisField.
 */
class AnimeSynopsisField extends StringField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Anime::ATTRIBUTE_SYNOPSIS);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The brief summary of the anime';
    }
}
