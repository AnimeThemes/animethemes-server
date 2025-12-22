<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\Theme\Schemas;

use App\Enums\Models\Wiki\ThemeType;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Fields\TextInput;
use App\Models\Wiki\Anime\AnimeTheme;

class ThemeForm
{
    public static function typeField(): Select
    {
        return Select::make(AnimeTheme::ATTRIBUTE_TYPE)
            ->label(__('filament.fields.anime_theme.type.name'))
            ->helperText(__('filament.fields.anime_theme.type.help'))
            ->options(ThemeType::class)
            ->required();
    }

    public static function sequenceField(): TextInput
    {
        return TextInput::make(AnimeTheme::ATTRIBUTE_SEQUENCE)
            ->label(__('filament.fields.anime_theme.sequence.name'))
            ->helperText(__('filament.fields.anime_theme.sequence.help'))
            ->integer();
    }
}
