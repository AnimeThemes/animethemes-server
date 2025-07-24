<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Anime;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\Wiki\Anime;

class AnimeSynopsisField extends StringField
{
    public function __construct()
    {
        parent::__construct(Anime::ATTRIBUTE_SYNOPSIS);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The brief summary of the anime';
    }
}
