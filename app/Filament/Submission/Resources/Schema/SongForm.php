<?php

declare(strict_types=1);

namespace App\Filament\Submission\Resources\Schema;

use App\Filament\Components\Fields\TextInput;
use App\Models\User\Submission\SubmissionSong;
use Filament\Schemas\Schema;

class SongForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make(SubmissionSong::ATTRIBUTE_TITLE)
                    ->label(__('filament.fields.song.title.name'))
                    ->helperText(__('filament.fields.song.title.help'))
                    ->required()
                    ->maxLength(192),

                TextInput::make(SubmissionSong::ATTRIBUTE_TITLE_NATIVE)
                    ->label(__('filament.fields.song.title_native.name'))
                    ->helperText(__('filament.fields.song.title_native.help'))
                    ->maxLength(192),
            ]);
    }
}
