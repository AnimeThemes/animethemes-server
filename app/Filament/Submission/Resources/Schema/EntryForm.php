<?php

declare(strict_types=1);

namespace App\Filament\Submission\Resources\Schema;

use App\Filament\Components\Fields\TextInput;
use App\Models\User\Submission\SubmissionAnimeThemeEntry;
use Filament\Forms\Components\Checkbox;
use Filament\Schemas\Schema;

class EntryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make(SubmissionAnimeThemeEntry::ATTRIBUTE_VERSION)
                    ->label(__('filament.fields.anime_theme_entry.version.name'))
                    ->helperText(__('filament.fields.anime_theme_entry.version.help'))
                    ->default(1)
                    ->integer()
                    ->required(),

                TextInput::make(SubmissionAnimeThemeEntry::ATTRIBUTE_EPISODES)
                    ->label(__('filament.fields.anime_theme_entry.episodes.name'))
                    ->helperText(__('filament.fields.anime_theme_entry.episodes.help'))
                    ->maxLength(192),

                Checkbox::make(SubmissionAnimeThemeEntry::ATTRIBUTE_NSFW)
                    ->label(__('filament.fields.anime_theme_entry.nsfw.name'))
                    ->helperText(__('filament.fields.anime_theme_entry.nsfw.help')),

                Checkbox::make(SubmissionAnimeThemeEntry::ATTRIBUTE_SPOILER)
                    ->label(__('filament.fields.anime_theme_entry.spoiler.name'))
                    ->helperText(__('filament.fields.anime_theme_entry.spoiler.help')),

                TextInput::make(SubmissionAnimeThemeEntry::ATTRIBUTE_NOTES)
                    ->label(__('filament.fields.anime_theme_entry.notes.name'))
                    ->helperText(__('filament.fields.anime_theme_entry.notes.help'))
                    ->maxLength(192),
            ])
            ->columns(1);
    }
}
