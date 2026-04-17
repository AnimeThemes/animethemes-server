<?php

declare(strict_types=1);

namespace App\Filament\Submission\Resources\Schema;

use App\Enums\Models\Wiki\ThemeType;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Fields\TextInput;
use App\Models\User\Submission\SubmissionAnimeTheme;
use App\Models\Wiki\Group;
use Filament\Schemas\Schema;

class ThemeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make(SubmissionAnimeTheme::ATTRIBUTE_TYPE)
                    ->label(__('filament.fields.anime_theme.type.name'))
                    ->helperText(__('filament.fields.anime_theme.type.help'))
                    ->options(ThemeType::class)
                    ->required(),

                TextInput::make(SubmissionAnimeTheme::ATTRIBUTE_SEQUENCE)
                    ->label(__('filament.fields.anime_theme.sequence.name'))
                    ->helperText(__('filament.fields.anime_theme.sequence.help'))
                    ->integer(),

                Select::make(SubmissionAnimeTheme::ATTRIBUTE_GROUP)
                    ->label(__('filament.resources.singularLabel.group'))
                    ->useScout($schema->getLivewire(), Group::class),
            ])
            ->columns(1);
    }
}
