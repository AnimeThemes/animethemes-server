<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Anime\Theme;

use App\Enums\Models\Wiki\ThemeType;
use App\GraphQL\Definition\Fields\EnumField;
use App\Models\Wiki\Anime\AnimeTheme;

class AnimeThemeTypeField extends EnumField
{
    public function __construct()
    {
        parent::__construct(AnimeTheme::ATTRIBUTE_TYPE, ThemeType::class, nullable: false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The type of the sequence';
    }
}
