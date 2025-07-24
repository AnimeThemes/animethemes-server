<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Anime\Theme;

use App\GraphQL\Definition\Fields\IntField;
use App\Models\Wiki\Anime\AnimeTheme;

class AnimeThemeSequenceField extends IntField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(AnimeTheme::ATTRIBUTE_SEQUENCE);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The numeric ordering of the theme';
    }
}
