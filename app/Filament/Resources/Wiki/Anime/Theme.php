<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime;

use App\Enums\Models\Wiki\ThemeType;
use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\BelongsTo;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Filters\NumberFilter;
use App\Filament\Components\Infolist\BelongsToEntry;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\RelationManagers\Wiki\Anime\ThemeRelationManager;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Anime as AnimeResource;
use App\Filament\Resources\Wiki\Anime\RelationManagers\ThemeAnimeRelationManager;
use App\Filament\Resources\Wiki\Anime\Theme\Entry;
use App\Filament\Resources\Wiki\Anime\Theme\Pages\ListThemes;
use App\Filament\Resources\Wiki\Anime\Theme\Pages\ViewTheme;
use App\Filament\Resources\Wiki\Anime\Theme\RelationManagers\EntryThemeRelationManager;
use App\Filament\Resources\Wiki\Artist as ArtistResource;
use App\Filament\Resources\Wiki\Group as GroupResource;
use App\Filament\Resources\Wiki\Song as SongResource;
use App\Filament\Resources\Wiki\Song\Performance as PerformanceResource;
use App\Filament\Resources\Wiki\Song\RelationManagers\PerformanceSongRelationManager;
use App\Filament\Resources\Wiki\Song\RelationManagers\ThemeSongRelationManager;
use App\Models\Wiki\Anime\AnimeTheme as ThemeModel;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Group;
use App\Models\Wiki\Song;
use App\Models\Wiki\Song\Membership;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

/**
 * Class Theme.
 */
class Theme extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string|null
     */
    protected static ?string $model = ThemeModel::class;

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getLabel(): string
    {
        return __('filament.resources.singularLabel.anime_theme');
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
        return __('filament.resources.label.anime_themes');
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
        return __('filament-icons.resources.anime_themes');
    }

    /**
     * Get the title for the resource.
     *
     * @param Model|null $record
     * @return string|null
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRecordTitle(?Model $record): ?string
    {
        return $record instanceof ThemeModel ? $record->anime->getName() . ' ' . $record->slug : null;
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
     */
    public static function getRecordSlug(): string
    {
        return 'anime-themes';
    }

    /**
     * Get the eloquent query for the resource.
     *
     * @return Builder
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Necessary to prevent lazy loading when loading related resources
        return $query->with([
            AnimeTheme::RELATION_ANIME,
            AnimeTheme::RELATION_GROUP,
            AnimeTheme::RELATION_ENTRIES,
            AnimeTheme::RELATION_SONG,
            'song.animethemes',
            'song.performances',
            'song.performances.artist' => function (MorphTo $morphTo) {
                $morphTo->morphWith([
                    Artist::class => [],
                    Membership::class => [Membership::RELATION_ARTIST, Membership::RELATION_MEMBER]
                ]);
            },
        ]);
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
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('theme')
                            ->label(__('filament.resources.singularLabel.anime_theme'))
                            ->schema([
                                BelongsTo::make(ThemeModel::ATTRIBUTE_ANIME)
                                    ->resource(AnimeResource::class)
                                    ->hiddenOn(ThemeRelationManager::class)
                                    ->required(),

                                Select::make(ThemeModel::ATTRIBUTE_TYPE)
                                    ->label(__('filament.fields.anime_theme.type.name'))
                                    ->helperText(__('filament.fields.anime_theme.type.help'))
                                    ->options(ThemeType::asSelectArray())
                                    ->required()
                                    ->enum(ThemeType::class)
                                    ->live(true)
                                    ->afterStateUpdated(fn (Set $set, Get $get) => Theme::setThemeSlug($set, $get)),

                                TextInput::make(ThemeModel::ATTRIBUTE_SEQUENCE)
                                    ->label(__('filament.fields.anime_theme.sequence.name'))
                                    ->helperText(__('filament.fields.anime_theme.sequence.help'))
                                    ->integer()
                                    ->live(true)
                                    ->afterStateUpdated(fn (Set $set, Get $get) => Theme::setThemeSlug($set, $get)),

                                TextInput::make(ThemeModel::ATTRIBUTE_SLUG)
                                    ->label(__('filament.fields.anime_theme.slug.name'))
                                    ->helperText(__('filament.fields.anime_theme.slug.help'))
                                    ->required()
                                    ->maxLength(192)
                                    ->alphaDash()
                                    ->readOnly(),

                                BelongsTo::make(ThemeModel::ATTRIBUTE_GROUP)
                                    ->resource(GroupResource::class)
                                    ->showCreateOption()
                                    ->live(true)
                                    ->afterStateUpdated(fn (Set $set, Get $get) => Theme::setThemeSlug($set, $get)),
                            ]),

                        Tab::make('song')
                            ->label(__('filament.resources.singularLabel.song'))
                            ->schema([
                                BelongsTo::make(ThemeModel::ATTRIBUTE_SONG)
                                    ->resource(SongResource::class)
                                    ->showCreateOption()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        /** @var Song|null $song */
                                        $song = Song::find($state);
                                        $set('performances', PerformanceSongRelationManager::formatArtists($song));
                                    }),

                                ...PerformanceResource::performancesFields(),
                            ]),

                        Tab::make('entries')
                            ->label(__('filament.resources.label.anime_theme_entries'))
                            ->schema([
                                Repeater::make(ThemeModel::RELATION_ENTRIES)
                                    ->label(__('filament.resources.label.anime_theme_entries'))
                                    ->addActionLabel(__('filament.buttons.add'))
                                    ->relationship()
                                    ->schema(Entry::form($form)->getComponents()),
                            ]),
                    ])
            ])
            ->columns(1);
    }

    /**
     * The index page of the resource.
     *
     * @param  Table  $table
     * @return Table
     */
    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                BelongsToColumn::make(ThemeModel::RELATION_ANIME, AnimeResource::class)
                    ->hiddenOn(ThemeAnimeRelationManager::class),

                TextColumn::make(ThemeModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(ThemeModel::ATTRIBUTE_TYPE)
                    ->label(__('filament.fields.anime_theme.type.name'))
                    ->formatStateUsing(fn (ThemeType $state) => $state->localize()),

                TextColumn::make(ThemeModel::ATTRIBUTE_SEQUENCE)
                    ->label(__('filament.fields.anime_theme.sequence.name')),

                TextColumn::make(ThemeModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.anime_theme.slug.name'))
                    ->formatStateUsing(fn ($record) => $record->getName()),

                BelongsToColumn::make(ThemeModel::RELATION_GROUP, GroupResource::class),

                BelongsToColumn::make(ThemeModel::RELATION_SONG, SongResource::class)
                    ->hiddenOn(ThemeSongRelationManager::class)
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
                        TextEntry::make(ThemeModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        BelongsToEntry::make(ThemeModel::RELATION_ANIME, AnimeResource::class),

                        TextEntry::make(ThemeModel::ATTRIBUTE_TYPE)
                            ->label(__('filament.fields.anime_theme.type.name'))
                            ->formatStateUsing(fn (ThemeType $state) => $state->localize()),

                        TextEntry::make(ThemeModel::ATTRIBUTE_SEQUENCE)
                            ->label(__('filament.fields.anime_theme.sequence.name')),

                        TextEntry::make(ThemeModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.anime_theme.slug.name'))
                            ->formatStateUsing(fn ($state) => ThemeModel::withTrashed()->find(intval($state))->getName()),

                        BelongsToEntry::make(ThemeModel::RELATION_GROUP, GroupResource::class)
                            ->label(__('filament.resources.singularLabel.group')),

                        BelongsToEntry::make(ThemeModel::RELATION_SONG, SongResource::class)
                            ->label(__('filament.resources.singularLabel.song')),
                    ])
                    ->columns(3),

                Section::make(__('filament.resources.singularLabel.song'))
                    ->schema([
                        RepeatableEntry::make(ThemeModel::RELATION_ARTISTS)
                            ->label('')
                            ->schema(ArtistResource::infolist($infolist)->getComponents())
                            ->columnSpanFull(),
                    ]),

                TimestampSection::make(),
            ]);
    }

    /**
     * Set the theme slug.
     *
     * @param  Set  $set
     * @param  Get  $get
     * @return void
     */
    protected static function setThemeSlug(Set $set, Get $get): void
    {
        $slug = Str::of('');
        $type = $get(ThemeModel::ATTRIBUTE_TYPE);

        if (!empty($type) || $type !== null) {
            $type = ThemeType::tryFrom(intval($type));
            $slug = $slug->append($type->name);
        }

        if ($slug->isNotEmpty() && $type !== ThemeType::IN) {
            $sequence = $get(ThemeModel::ATTRIBUTE_SEQUENCE);
            $slug = $slug->append(strval(empty($sequence) ? 1 : $sequence));
        }

        if ($slug->isNotEmpty()) {
            $group = $get(ThemeModel::ATTRIBUTE_GROUP);

            if (!empty($group)) {
                $slug = $slug->append('-' . Group::find(intval($group))->slug);
            }
        }

        $set(ThemeModel::ATTRIBUTE_SLUG, $slug->__toString());
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
            RelationGroup::make(static::getLabel(),[
                EntryThemeRelationManager::class,

                ...parent::getBaseRelations(),
            ]),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     */
    public static function getFilters(): array
    {
        return [
            SelectFilter::make(ThemeModel::ATTRIBUTE_TYPE)
                ->label(__('filament.fields.anime_theme.type.name'))
                ->options(ThemeType::asSelectArray()),

            NumberFilter::make(ThemeModel::ATTRIBUTE_SEQUENCE)
                ->label(__('filament.fields.anime_theme.sequence.name')),

            Filter::make(ThemeType::IN->localize())
                ->label(__('filament.filters.anime_theme.without_in'))
                ->query(fn (Builder $query) => $query->whereNot(ThemeModel::ATTRIBUTE_TYPE, ThemeType::IN->value))
                ->default(true),

            ...parent::getFilters(),
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array
     */
    public static function getActions(): array
    {
        return [
            ...parent::getActions(),
        ];
    }

    /**
     * Get the bulk actions available for the resource.
     *
     * @param  array|null  $actionsIncludedInGroup
     * @return array
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return [
            ...parent::getBulkActions(),
        ];
    }

    /**
     * Get the table actions available for the resource.
     *
     * @return array
     */
    public static function getTableActions(): array
    {
        return [
            ...parent::getTableActions(),
        ];
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
            'index' => ListThemes::route('/'),
            'view' => ViewTheme::route('/{record:theme_id}'),
        ];
    }
}
