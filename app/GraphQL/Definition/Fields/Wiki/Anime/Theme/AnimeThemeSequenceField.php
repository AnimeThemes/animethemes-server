<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Anime\Theme;

use App\GraphQL\Definition\Fields\IntField;
use App\Models\Wiki\Anime\AnimeTheme;

class AnimeThemeSequenceField extends IntField
{
    public function __construct()
    {
        parent::__construct(AnimeTheme::ATTRIBUTE_SEQUENCE);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The numeric ordering of the theme';
    }
}
