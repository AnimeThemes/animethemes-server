<?php

declare(strict_types=1);

namespace App\Filament\Submission\Resources;

use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Submission\Resources\Anime\Pages\CreateAnimeSubmission;
use App\Filament\Submission\Resources\Anime\Pages\ListAnimeSubmissions;
use App\Models\Wiki\Anime as AnimeModel;
use Filament\QueryBuilder\Constraints\NumberConstraint;
use Filament\QueryBuilder\Constraints\SelectConstraint;
use Filament\QueryBuilder\Constraints\TextConstraint;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AnimeSubmissionResource extends BaseSubmissionResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = AnimeModel::class;

    public static function getModelLabel(): string
    {
        return __('submissions.resources.singularLabel.anime');
    }

    public static function getPluralModelLabel(): string
    {
        return __('submissions.resources.label.anime');
    }

    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::OutlinedTv;
    }

    public static function getRecordTitleAttribute(): string
    {
        return AnimeModel::ATTRIBUTE_NAME;
    }

    public static function canGloballySearch(): bool
    {
        return true;
    }

    public static function getRecordSlug(): string
    {
        return 'anime';
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make(AnimeModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(AnimeModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.anime.name.name'))
                    ->copyableWithMessage()
                    ->limit(50)
                    ->tooltip(fn (TextColumn $column): mixed => $column->getState()),

                TextColumn::make(AnimeModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.anime.slug.name'))
                    ->limit(20)
                    ->tooltip(fn (TextColumn $column): mixed => $column->getState()),

                TextColumn::make(AnimeModel::ATTRIBUTE_YEAR)
                    ->label(__('filament.fields.anime.year.name')),

                TextColumn::make(AnimeModel::ATTRIBUTE_SEASON)
                    ->label(__('filament.fields.anime.season.name'))
                    ->formatStateUsing(fn (AnimeSeason $state): string => $state->localizeStyled())
                    ->html(),

                TextColumn::make(AnimeModel::ATTRIBUTE_MEDIA_FORMAT)
                    ->label(__('filament.fields.anime.media_format.name'))
                    ->formatStateUsing(fn (AnimeMediaFormat $state): ?string => $state->localize()),
            ])
            ->searchable();
    }

    /**
     * @return \Filament\Tables\Filters\BaseFilter[]
     */
    public static function getFilters(): array
    {
        return [
            QueryBuilder::make()
                ->constraints([
                    TextConstraint::make(AnimeModel::ATTRIBUTE_NAME)
                        ->label(__('filament.fields.anime.name.name')),

                    TextConstraint::make(AnimeModel::ATTRIBUTE_SLUG)
                        ->label(__('filament.fields.anime.slug.name')),

                    NumberConstraint::make(AnimeModel::ATTRIBUTE_YEAR)
                        ->label(__('filament.fields.anime.year.name')),

                    SelectConstraint::make(AnimeModel::ATTRIBUTE_SEASON)
                        ->label(__('filament.fields.anime.season.name'))
                        ->options(AnimeSeason::class)
                        ->multiple(),

                    SelectConstraint::make(AnimeModel::ATTRIBUTE_MEDIA_FORMAT)
                        ->label(__('filament.fields.anime.media_format.name'))
                        ->options(AnimeMediaFormat::class)
                        ->multiple(),

                    TextConstraint::make(AnimeModel::ATTRIBUTE_SYNOPSIS)
                        ->label(__('filament.fields.anime.synopsis.name')),

                    ...parent::getConstraints(),
                ]),

            ...parent::getFilters(),
        ];
    }

    /**
     * @return array<string, \Filament\Resources\Pages\PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListAnimeSubmissions::route('/'),
            'create' => CreateAnimeSubmission::route('/submit'),
        ];
    }
}
