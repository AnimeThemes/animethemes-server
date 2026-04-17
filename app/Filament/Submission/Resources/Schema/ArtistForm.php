<?php

declare(strict_types=1);

namespace App\Filament\Submission\Resources\Schema;

use App\Filament\Components\Fields\TextInput;
use App\Models\User\Submission\SubmissionArtist;
use App\Models\Wiki\Artist;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Schemas\Schema;

class ArtistForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make(SubmissionArtist::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.artist.name.name'))
                    ->helperText(__('filament.fields.artist.name.help'))
                    ->required()
                    ->maxLength(192)
                    ->columnSpan(1)
                    ->afterStateUpdatedJs(<<<'JS'
                        $set('slug', slug($state ?? ''));
                    JS),

                TextInput::make(SubmissionArtist::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.artist.slug.name'))
                    ->helperText(__('filament.fields.artist.slug.help'))
                    ->columnSpan(1)
                    ->required()
                    ->alphaDash()
                    ->maxLength(192)
                    ->unique(Artist::TABLE, Artist::ATTRIBUTE_SLUG),

                MarkdownEditor::make(SubmissionArtist::ATTRIBUTE_INFORMATION)
                    ->label(__('filament.fields.artist.information.name'))
                    ->helperText(__('filament.fields.artist.information.help'))
                    ->columnSpanFull()
                    ->maxLength(65535),
            ])
            ->columns(3);
    }
}
