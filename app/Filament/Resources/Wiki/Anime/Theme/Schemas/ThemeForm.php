<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\Theme\Schemas;

use App\Enums\Models\Wiki\ThemeType;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Fields\TextInput;
use App\Filament\Resources\Wiki\Anime\Theme;
use App\Models\Wiki\Anime\AnimeTheme;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class ThemeForm
{
    public static function typeField(): Select
    {
        return Select::make(AnimeTheme::ATTRIBUTE_TYPE)
            ->label(__('filament.fields.anime_theme.type.name'))
            ->helperText(__('filament.fields.anime_theme.type.help'))
            ->options(ThemeType::class)
            ->required()
            ->live()
            ->partiallyRenderComponentsAfterStateUpdated([AnimeTheme::ATTRIBUTE_SLUG])
            ->afterStateUpdated(fn (Set $set, Get $get) => Theme::setThemeSlug($set, $get));
    }

    public static function sequenceField(): TextInput
    {
        return TextInput::make(AnimeTheme::ATTRIBUTE_SEQUENCE)
            ->label(__('filament.fields.anime_theme.sequence.name'))
            ->helperText(__('filament.fields.anime_theme.sequence.help'))
            ->integer()
            ->live()
            ->partiallyRenderComponentsAfterStateUpdated([AnimeTheme::ATTRIBUTE_SLUG])
            ->afterStateUpdated(fn (Set $set, Get $get) => Theme::setThemeSlug($set, $get));
    }

    public static function slugField(): TextInput
    {
        return TextInput::make(AnimeTheme::ATTRIBUTE_SLUG)
            ->label(__('filament.fields.anime_theme.slug.name'))
            ->helperText(__('filament.fields.anime_theme.slug.help'))
            ->required()
            ->maxLength(192)
            ->alphaDash()
            ->readOnly();
    }
}
