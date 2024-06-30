<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Actions\Discord\DiscordThreadAction;
use App\Filament\Actions\Models\Wiki\Anime\AttachAnimeImageAction;
use App\Filament\Actions\Models\Wiki\Anime\AttachAnimeResourceAction;
use App\Filament\Actions\Models\Wiki\Anime\BackfillAnimeAction;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Filters\NumberFilter;
use App\Filament\Components\Infolist\TextEntry;
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
use App\Filament\Resources\Wiki\ExternalResource\RelationManagers\AnimeResourceRelationManager;
use App\Models\Wiki\Anime as AnimeModel;
use App\Pivots\Wiki\AnimeResource;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Tables\Actions\ActionGroup;
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
     * The icon displayed to the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getNavigationIcon(): string
    {
        return __('filament.resources.icon.anime');
    }

    /**
     * Get the title attribute for the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRecordTitleAttribute(): string
    {
        return AnimeModel::ATTRIBUTE_NAME;
    }

    /**
     * Determine if the resource can globally search.
     *
     * @return bool
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function canGloballySearch(): bool
    {
        return true;
    }

    /**
     * Get the slug (URI key) for the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRecordSlug(): string
    {
        return 'anime';
    }

    /**
     * Get the route key for the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRecordRouteKeyName(): string
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
                    ->maxLength(192)
                    ->rules(['required', 'max:192'])
                    ->live(true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set(AnimeModel::ATTRIBUTE_SLUG, Str::slug($state, '_'))),

                TextInput::make(AnimeModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.anime.slug.name'))
                    ->helperText(__('filament.fields.anime.slug.help'))
                    ->required()
                    ->unique(AnimeModel::class, AnimeModel::ATTRIBUTE_SLUG, ignoreRecord: true)
                    ->rules([
                        fn ($record) => [
                            'required',
                            'max:192',
                            'alpha_dash',
                            $record !== null
                                ? Rule::unique(AnimeModel::class)
                                    ->ignore($record->getKey(), AnimeModel::ATTRIBUTE_ID)
                                    ->__toString()
                                : Rule::unique(AnimeModel::class)->__toString(),
                        ]
                    ]),

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
                    ->options(AnimeSeason::asSelectArrayStyled())
                    ->searchable()
                    ->allowHtml()
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
                    ->visibleOn(AnimeResourceRelationManager::class)
                    ->placeholder('-'),
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
                    ->sortable(),

                TextColumn::make(AnimeModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.anime.name.name'))
                    ->sortable()
                    ->copyableWithMessage()
                    ->toggleable()
                    ->limit(50)
                    ->tooltip(fn (TextColumn $column) => $column->getState()),

                TextColumn::make(AnimeModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.anime.slug.name'))
                    ->sortable()
                    ->toggleable()
                    ->limit(20)
                    ->tooltip(fn (TextColumn $column) => $column->getState()),

                TextColumn::make(AnimeModel::ATTRIBUTE_YEAR)
                    ->label(__('filament.fields.anime.year.name'))
                    ->sortable()
                    ->toggleable(),

                TextColumn::make(AnimeModel::ATTRIBUTE_SEASON)
                    ->label(__('filament.fields.anime.season.name'))
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(fn ($state) => $state->localizeStyled())
                    ->html(),

                TextColumn::make(AnimeModel::ATTRIBUTE_MEDIA_FORMAT)
                    ->label(__('filament.fields.anime.media_format.name'))
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(fn ($state) => $state->localize()),

                TextColumn::make(AnimeModel::ATTRIBUTE_SYNOPSIS)
                    ->label(__('filament.fields.anime.synopsis.name'))
                    ->hidden(),

                TextColumn::make(AnimeResource::ATTRIBUTE_AS)
                    ->label(__('filament.fields.anime.resources.as.name'))
                    ->visibleOn(AnimeResourceRelationManager::class)
                    ->placeholder('-'),
            ])
            ->searchable();
    }

    /**
     * Get the infolist available for the resource.
     *
     * @param  Infolist  $infolist
     * @return Infolist
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make(static::getRecordTitle($infolist->getRecord()))
                    ->schema([
                        TextEntry::make(AnimeModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(AnimeModel::ATTRIBUTE_NAME)
                            ->label(__('filament.fields.anime.name.name'))
                            ->copyableWithMessage(),

                        TextEntry::make(AnimeModel::ATTRIBUTE_SLUG)
                            ->label(__('filament.fields.anime.slug.name'))
                            ->limit(60),

                        TextEntry::make(AnimeModel::ATTRIBUTE_YEAR)
                            ->label(__('filament.fields.anime.year.name')),

                        TextEntry::make(AnimeModel::ATTRIBUTE_SEASON)
                            ->label(__('filament.fields.anime.season.name'))
                            ->formatStateUsing(fn ($state) => $state->localizeStyled())
                            ->html(),

                        TextEntry::make(AnimeModel::ATTRIBUTE_MEDIA_FORMAT)
                            ->label(__('filament.fields.anime.media_format.name'))
                            ->formatStateUsing(fn ($state) => $state->localize()),

                        TextEntry::make(AnimeModel::ATTRIBUTE_SYNOPSIS)
                            ->label(__('filament.fields.anime.synopsis.name'))
                            ->markdown()
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Section::make(__('filament.fields.base.timestamps'))
                    ->schema(parent::timestamps())
                    ->columns(3),
            ]);
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
            RelationGroup::make(static::getLabel(),
                array_merge(
                    [
                        SynonymAnimeRelationManager::class,
                        ThemeAnimeRelationManager::class,
                        SeriesAnimeRelationManager::class,
                        ResourceAnimeRelationManager::class,
                        ImageAnimeRelationManager::class,
                        StudioAnimeRelationManager::class,
                    ],
                    parent::getBaseRelations(),
                )
            ),
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
            [
                NumberFilter::make(AnimeModel::ATTRIBUTE_YEAR)
                    ->labels(__('filament.filters.anime.year_from'), __('filament.filters.anime.year_to')),

                SelectFilter::make(AnimeModel::ATTRIBUTE_SEASON)
                    ->label(__('filament.fields.anime.season.name'))
                    ->options(AnimeSeason::asSelectArray()),

                SelectFilter::make(AnimeModel::ATTRIBUTE_MEDIA_FORMAT)
                    ->label(__('filament.fields.anime.media_format.name'))
                    ->options(AnimeMediaFormat::asSelectArray()),
            ],
            parent::getFilters(),
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
        $streamingResourceSites = [
            ResourceSite::CRUNCHYROLL,
            ResourceSite::HIDIVE,
            ResourceSite::NETFLIX,
            ResourceSite::DISNEY_PLUS,
            ResourceSite::HULU,
            ResourceSite::AMAZON_PRIME_VIDEO,
        ];

        return array_merge(
            parent::getActions(),
            [
                ActionGroup::make([
                    DiscordThreadAction::make('discord-thread'),

                    BackfillAnimeAction::make('backfill-anime'),

                    AttachAnimeImageAction::make('attach-anime-image'),

                    AttachAnimeResourceAction::make('attach-anime-resource'),

                    AttachAnimeResourceAction::make('attach-anime-streaming-resource')
                        ->label(__('filament.actions.models.wiki.attach_streaming_resource.name'))
                        ->icon('heroicon-o-tv')
                        ->sites($streamingResourceSites),
                ]),
            ],
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
     * Get the header actions available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getHeaderActions(): array
    {
        return array_merge(
            parent::getHeaderActions(),
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
