<?php

declare(strict_types=1);

namespace App\Filament\Submission\Resources\Schema;

use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Fields\TextInput;
use App\Models\User\Submission\SubmissionAnime;
use App\Models\Wiki\Anime;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Schemas\Schema;

class AnimeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make(SubmissionAnime::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.anime.name.name'))
                    ->helperText(__('filament.fields.anime.name.help'))
                    ->required()
                    ->maxLength(255)
                    ->afterStateUpdatedJs(<<<'JS'
                        $set('slug', slug($state ?? ''));
                    JS),

                TextInput::make(SubmissionAnime::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.anime.slug.name'))
                    ->helperText(__('filament.fields.anime.slug.help'))
                    ->required()
                    ->alphaDash()
                    ->maxLength(192)
                    ->unique(Anime::TABLE, Anime::ATTRIBUTE_SLUG),

                TextInput::make(SubmissionAnime::ATTRIBUTE_YEAR)
                    ->label(__('filament.fields.anime.year.name'))
                    ->helperText(__('filament.fields.anime.year.help'))
                    ->required()
                    ->integer()
                    ->length(4)
                    ->default(date('Y'))
                    ->minValue(1960)
                    ->maxValue(intval(date('Y')) + 1),

                Select::make(SubmissionAnime::ATTRIBUTE_SEASON)
                    ->label(__('filament.fields.anime.season.name'))
                    ->helperText(__('filament.fields.anime.season.help'))
                    ->options(AnimeSeason::asSelectArrayStyled())
                    ->required()
                    ->enum(AnimeSeason::class)
                    ->default(AnimeSeason::getCurrentSeason())
                    ->searchable()
                    ->allowHtml(),

                Select::make(SubmissionAnime::ATTRIBUTE_FORMAT)
                    ->label(__('filament.fields.anime.format.name'))
                    ->helperText(__('filament.fields.anime.format.help'))
                    ->options(AnimeMediaFormat::class)
                    ->required(),

                MarkdownEditor::make(SubmissionAnime::ATTRIBUTE_SYNOPSIS)
                    ->label(__('filament.fields.anime.synopsis.name'))
                    ->helperText(__('filament.fields.anime.synopsis.help'))
                    ->columnSpan(2)
                    ->maxLength(65535),
            ])
            ->columns(2);
    }
}
