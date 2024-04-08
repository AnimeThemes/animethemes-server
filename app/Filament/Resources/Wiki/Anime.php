<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Anime\Pages\CreateAnime;
use App\Filament\Resources\Wiki\Anime\Pages\EditAnime;
use App\Filament\Resources\Wiki\Anime\Pages\ListAnimes;
use App\Filament\Resources\Wiki\Anime\Pages\ViewAnime;
use App\Filament\Resources\Wiki\Anime\RelationManagers\ImageAnimeRelationManager;
use App\Filament\Resources\Wiki\Anime\RelationManagers\ResourceAnimeRelationManager;
use App\Filament\Resources\Wiki\Anime\RelationManagers\SeriesAnimeRelationManager;
use App\Filament\Resources\Wiki\Anime\RelationManagers\StudioAnimeRelationManager;
use App\Filament\Resources\Wiki\Anime\RelationManagers\SynonymAnimeRelationManager;
use App\Filament\Resources\Wiki\Anime\RelationManagers\ThemeAnimeRelationManager;
use App\Filament\Resources\Wiki\Anime\Theme as ThemeResource;
use App\Filament\Resources\Wiki\ExternalResource\RelationManagers\AnimeResourceRelationManager;
use App\Models\Wiki\Anime as AnimeModel;
use App\Pivots\Wiki\AnimeResource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

/**
 * Class Anime.
 */
class Anime extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string|null
     */
    protected static ?string $model = AnimeModel::class;

    /**
     * The icon displayed to the resource.
     *
     * @var string|null
     */
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getLabel(): string
    {
        return __('filament.resources.singularLabel.anime');
    }

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getPluralLabel(): string
    {
        return __('filament.resources.label.anime');
    }

    /**
     * The logical group associated with the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getNavigationGroup(): string
    {
        return __('filament.resources.group.wiki');
    }

    /**
     * Get the title attribute for the resource.
     *
     * @return string|null
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRecordTitleAttribute(): ?string
    {
        return AnimeModel::ATTRIBUTE_NAME;
    }

    /**
     * Get the attributes available for the global search.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getGloballySearchableAttributes(): array
    {
        return [AnimeModel::ATTRIBUTE_NAME];
    }

    /**
     * Get the slug (URI key) for the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getSlug(): string
    {
        return 'anime';
    }

    /**
     * Get the route key for the resource.
     *
     * @return string|null
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRecordRouteKeyName(): ?string
    {
        return AnimeModel::ATTRIBUTE_ID;
    }

    /**
     * The form to the actions.
     *
     * @param  Form  $form
     * @return Form
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make(AnimeModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.anime.name.name'))
                    ->helperText(__('filament.fields.anime.name.help'))
                    ->required()
                    ->rules(['required', 'max:192'])
                    ->maxLength(192)
                    ->live(true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set(AnimeModel::ATTRIBUTE_SLUG, Str::slug($state, '_'))),

                TextInput::make(AnimeModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.anime.slug.name'))
                    ->helperText(__('filament.fields.anime.slug.help'))
                    ->required()
                    ->rules(['required', 'max:192', 'alpha_dash', Rule::unique(AnimeModel::class)]),

                TextInput::make(AnimeModel::ATTRIBUTE_YEAR)
                    ->label(__('filament.fields.anime.year.name'))
                    ->helperText(__('filament.fields.anime.year.help'))
                    ->numeric()
                    ->required()
                    ->rules(['required', 'digits:4', 'integer'])
                    ->minValue(1960)
                    ->maxValue(intval(date('Y')) + 1),

                Select::make(AnimeModel::ATTRIBUTE_SEASON)
                    ->label(__('filament.fields.anime.season.name'))
                    ->helperText(__('filament.fields.anime.season.help'))
                    ->options(AnimeSeason::asSelectArray())
                    ->required()
                    ->rules(['required', new Enum(AnimeSeason::class)]),

                Select::make(AnimeModel::ATTRIBUTE_MEDIA_FORMAT)
                    ->label(__('filament.fields.anime.media_format.name'))
                    ->helperText(__('filament.fields.anime.media_format.help'))
                    ->options(AnimeMediaFormat::asSelectArray())
                    ->required()
                    ->rules(['required', new Enum(AnimeMediaFormat::class)]),

                MarkdownEditor::make(AnimeModel::ATTRIBUTE_SYNOPSIS)
                    ->label(__('filament.fields.anime.synopsis.name'))
                    ->helperText(__('filament.fields.anime.synopsis.help'))
                    ->columnSpan(2)
                    ->maxLength(65535)
                    ->rules('max:65535'),

                TextInput::make(AnimeResource::ATTRIBUTE_AS)
                    ->label(__('filament.fields.anime.resources.as.name'))
                    ->helperText(__('filament.fields.anime.resources.as.help'))
                    ->visibleOn(AnimeResourceRelationManager::class),
            ])
            ->columns(2);
    }

    /**
     * The index page of the resource.
     *
     * @param  Table  $table
     * @return Table
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make(AnimeModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id'))
                    ->numeric()
                    ->sortable(),

                TextColumn::make(AnimeModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.anime.name.name'))
                    ->sortable()
                    ->searchable()
                    ->copyable(),

                TextColumn::make(AnimeModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.anime.slug.name'))
                    ->sortable()
                    ->copyable(),

                TextColumn::make(AnimeModel::ATTRIBUTE_YEAR)
                    ->label(__('filament.fields.anime.year.name'))
                    ->numeric()
                    ->sortable(),

                SelectColumn::make(AnimeModel::ATTRIBUTE_SEASON)
                    ->label(__('filament.fields.anime.season.name'))
                    ->options(AnimeSeason::asSelectArray())
                    ->sortable(),

                SelectColumn::make(AnimeModel::ATTRIBUTE_MEDIA_FORMAT)
                    ->label(__('filament.fields.anime.media_format.name'))
                    ->options(AnimeMediaFormat::asSelectArray())
                    ->sortable(),

                TextColumn::make(AnimeModel::ATTRIBUTE_SYNOPSIS)
                    ->label(__('filament.fields.anime.synopsis.name'))
                    ->hidden(),

                TextColumn::make(AnimeResource::ATTRIBUTE_AS)
                    ->label(__('filament.fields.anime.resources.as.name'))
                    ->visibleOn(AnimeResourceRelationManager::class),
            ])
            ->defaultSort(AnimeModel::ATTRIBUTE_ID, 'desc')
            ->filters(static::getFilters())
            ->actions(static::getActions())
            ->bulkActions(static::getBulkActions());
    }

    /**
     * Get the relationships available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRelations(): array
    {
        return [
            RelationGroup::make(static::getLabel(), [
                SynonymAnimeRelationManager::class,
                ThemeAnimeRelationManager::class,
                SeriesAnimeRelationManager::class,
                ResourceAnimeRelationManager::class,
                ImageAnimeRelationManager::class,
                StudioAnimeRelationManager::class,
            ]),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getFilters(): array
    {
        return array_merge(
            parent::getFilters(),
            [
                SelectFilter::make(AnimeModel::ATTRIBUTE_SEASON)
                    ->label(__('filament.fields.anime.season.name'))
                    ->options(AnimeSeason::asSelectArray()),

                SelectFilter::make(AnimeModel::ATTRIBUTE_MEDIA_FORMAT)
                    ->label(__('filament.fields.anime.media_format.name'))
                    ->options(AnimeMediaFormat::asSelectArray()),
            ]
        );
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getActions(): array
    {
        return array_merge(
            parent::getActions(),
            [],
        );
    }

    /**
     * Get the bulk actions available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getBulkActions(): array
    {
        return array_merge(
            parent::getBulkActions(),
            [],
        );
    }

    /**
     * Get the pages available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getPages(): array
    {
        return [
            'index' => ListAnimes::route('/'),
            'create' => CreateAnime::route('/create'),
            'view' => ViewAnime::route('/{record:anime_id}'),
            'edit' => EditAnime::route('/{record:anime_id}/edit'),
        ];
    }
}
