<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Theme\Schemas;

use App\Enums\Models\Wiki\ThemeType;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Fields\TextInput;
use App\Models\Wiki\Theme;

class ThemeForm
{
    public static function typeField(): Select
    {
        return Select::make(Theme::ATTRIBUTE_TYPE)
            ->label(__('filament.fields.theme.type.name'))
            ->helperText(__('filament.fields.theme.type.help'))
            ->options(ThemeType::class)
            ->required();
    }

    public static function sequenceField(): TextInput
    {
        return TextInput::make(Theme::ATTRIBUTE_SEQUENCE)
            ->label(__('filament.fields.theme.sequence.name'))
            ->helperText(__('filament.fields.theme.sequence.help'))
            ->integer();
    }
}
