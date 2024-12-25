<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime;

use App\Enums\Models\Wiki\ThemeType;
use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\BelongsTo;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Filters\NumberFilter;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\RelationManagers\Wiki\Anime\ThemeRelationManager;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Anime as AnimeResource;
use App\Filament\Resources\Wiki\Anime\RelationManagers\ThemeAnimeRelationManager;
use App\Filament\Resources\Wiki\Anime\Theme\Entry;
use App\Filament\Resources\Wiki\Anime\Theme\Pages\CreateTheme;
use App\Filament\Resources\Wiki\Anime\Theme\Pages\EditTheme;
use App\Filament\Resources\Wiki\Anime\Theme\Pages\ListThemes;
use App\Filament\Resources\Wiki\Anime\Theme\Pages\ViewTheme;
use App\Filament\Resources\Wiki\Anime\Theme\RelationManagers\EntryThemeRelationManager;
use App\Filament\Resources\Wiki\Artist as ArtistResource;
use App\Filament\Resources\Wiki\Group as GroupResource;
use App\Filament\Resources\Wiki\Song as SongResource;
use App\Filament\Resources\Wiki\Song\RelationManagers\ThemeSongRelationManager;
use App\Models\Wiki\Anime as AnimeModel;
use App\Models\Wiki\Anime\AnimeTheme as ThemeModel;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Group;
use App\Models\Wiki\Song;
use App\Pivots\Wiki\ArtistSong;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;

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
        return __('filament.resources.icon.anime_themes');
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
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRecordSlug(): string
    {
        return 'anime-themes';
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
                                    ->required()
                                    ->rules(['required']),

                                Select::make(ThemeModel::ATTRIBUTE_TYPE)
                                    ->label(__('filament.fields.anime_theme.type.name'))
                                    ->helperText(__('filament.fields.anime_theme.type.help'))
                                    ->options(ThemeType::asSelectArray())
                                    ->required()
                                    ->live(true)
                                    ->afterStateUpdated(fn (Set $set, Get $get) => Theme::setThemeSlug($set, $get))
                                    ->rules(['required', new Enum(ThemeType::class)]),

                                TextInput::make(ThemeModel::ATTRIBUTE_SEQUENCE)
                                    ->label(__('filament.fields.anime_theme.sequence.name'))
                                    ->helperText(__('filament.fields.anime_theme.sequence.help'))
                                    ->numeric()
                                    ->live(true)
                                    ->afterStateUpdated(fn (Set $set, Get $get) => Theme::setThemeSlug($set, $get))
                                    ->rules(['nullable', 'integer']),

                                TextInput::make(ThemeModel::ATTRIBUTE_SLUG)
                                    ->label(__('filament.fields.anime_theme.slug.name'))
                                    ->helperText(__('filament.fields.anime_theme.slug.help'))
                                    ->required()
                                    ->readOnly()
                                    ->maxLength(192)
                                    ->rules(['required', 'max:192', 'alpha_dash']),

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

                                        if (!$song) return;

                                        foreach ($song->artists()->get() as $index => $artist) {
                                            $set("song.artists.item{$index}.artist_id", $artist->getKey());
                                            $set("song.artists.item{$index}.as", Arr::get($artist, 'artistsong.as'));
                                        }
                                    }),

                                Repeater::make(ThemeModel::RELATION_SONG . '.' . Song::RELATION_ARTISTS)
                                    ->label(__('filament.resources.label.artists'))
                                    ->addActionLabel(__('filament.buttons.add').' '.__('filament.resources.singularLabel.artist'))
                                    ->hidden(fn (Get $get) => $get(ThemeModel::ATTRIBUTE_SONG) === null)
                                    ->live(true)
                                    ->key('song.artists')
                                    ->collapsible()
                                    ->defaultItems(0)
                                    ->schema([
                                        BelongsTo::make(Artist::ATTRIBUTE_ID)
                                            ->resource(ArtistResource::class)
                                            ->showCreateOption()
                                            ->required()
                                            ->rules(['required']),

                                        TextInput::make(ArtistSong::ATTRIBUTE_AS)
                                            ->label(__('filament.fields.artist.songs.as.name'))
                                            ->helperText(__('filament.fields.artist.songs.as.help')),

                                        TextInput::make(ArtistSong::ATTRIBUTE_ALIAS)
                                            ->label(__('filament.fields.artist.songs.alias.name'))
                                            ->helperText(__('filament.fields.artist.songs.alias.help')),
                                    ])
                                    ->formatStateUsing(function (?array $state, Get $get) {
                                        /** @var Song|null $song */
                                        $song = Song::find($get(ThemeModel::ATTRIBUTE_SONG));

                                        if (!$song) return $state;

                                        $artists = [];
                                        foreach ($song->artists()->get() as $artist) {
                                            $artists[] = [
                                                Artist::ATTRIBUTE_ID => $artist->getKey(),
                                                ArtistSong::ATTRIBUTE_ALIAS => Arr::get($artist, 'artistsong.alias'),
                                                ArtistSong::ATTRIBUTE_AS => Arr::get($artist, 'artistsong.as'),
                                            ];
                                        }

                                        return $artists;
                                    })
                                    ->saveRelationshipsUsing(function (?array $state, Get $get) {
                                        /** @var Song $song */
                                        $song = Song::find($get(ThemeModel::ATTRIBUTE_SONG));

                                        $artists = [];
                                        foreach ($state as $artist) {
                                            $artists[Arr::get($artist, Artist::ATTRIBUTE_ID)] = [
                                                ArtistSong::ATTRIBUTE_ALIAS => Arr::get($artist, ArtistSong::ATTRIBUTE_ALIAS),
                                                ArtistSong::ATTRIBUTE_AS => Arr::get($artist, ArtistSong::ATTRIBUTE_AS)
                                            ];
                                        }

                                        $song->artists()->sync($artists);
                                    })
                                    ->columns(3),
                            ]),

                        Tab::make('entries')
                            ->label(__('filament.resources.label.anime_theme_entries'))
                            ->hiddenOn(EditTheme::class)
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
                BelongsToColumn::make(ThemeModel::RELATION_ANIME . '.' . AnimeModel::ATTRIBUTE_NAME)
                    ->resource(AnimeResource::class)
                    ->hiddenOn(ThemeAnimeRelationManager::class),

                TextColumn::make(ThemeModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(ThemeModel::ATTRIBUTE_TYPE)
                    ->label(__('filament.fields.anime_theme.type.name'))
                    ->formatStateUsing(fn ($state) => $state->localize()),

                TextColumn::make(ThemeModel::ATTRIBUTE_SEQUENCE)
                    ->label(__('filament.fields.anime_theme.sequence.name')),

                TextColumn::make(ThemeModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.anime_theme.slug.name'))
                    ->formatStateUsing(fn ($state, $record) => $record->getName()),

                BelongsToColumn::make(ThemeModel::RELATION_GROUP . '.' . Group::ATTRIBUTE_NAME)
                    ->resource(GroupResource::class),

                BelongsToColumn::make(ThemeModel::RELATION_SONG . '.' . Song::ATTRIBUTE_TITLE)
                    ->resource(SongResource::class)
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

                        TextEntry::make(ThemeModel::RELATION_ANIME . '.' . AnimeModel::ATTRIBUTE_NAME)
                            ->label(__('filament.resources.singularLabel.anime'))
                            ->urlToRelated(AnimeResource::class, ThemeModel::RELATION_ANIME),

                        TextEntry::make(ThemeModel::ATTRIBUTE_TYPE)
                            ->label(__('filament.fields.anime_theme.type.name'))
                            ->formatStateUsing(fn ($state) => $state->localize()),

                        TextEntry::make(ThemeModel::ATTRIBUTE_SEQUENCE)
                            ->label(__('filament.fields.anime_theme.sequence.name')),

                        TextEntry::make(ThemeModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.anime_theme.slug.name'))
                            ->formatStateUsing(fn ($state) => ThemeModel::withTrashed()->find(intval($state))->getName()),

                        TextEntry::make(ThemeModel::RELATION_GROUP . '.' . Group::ATTRIBUTE_NAME)
                            ->label(__('filament.resources.singularLabel.group'))
                            ->urlToRelated(GroupResource::class, ThemeModel::RELATION_GROUP),

                        TextEntry::make(ThemeModel::RELATION_SONG . '.' . Song::ATTRIBUTE_TITLE)
                            ->label(__('filament.resources.singularLabel.song'))
                            ->urlToRelated(SongResource::class, ThemeModel::RELATION_SONG),
                    ])
                    ->columns(3),

                Section::make(__('filament.resources.singularLabel.song'))
                    ->relationship(ThemeModel::RELATION_SONG)
                    ->schema([
                        RepeatableEntry::make(__('filament.resources.label.artists'))
                            ->schema([
                                TextEntry::make(Artist::ATTRIBUTE_ID)
                                    ->label(__('filament.fields.base.id')),

                                TextEntry::make(Artist::ATTRIBUTE_NAME)
                                    ->label(__('filament.fields.artist.name.name'))
                                    ->urlToRelated(ArtistResource::class, '')
                                    ->formatStateUsing(fn ($state) => "<p style='color: rgb(64, 184, 166);'>{$state}</p>"),

                                TextEntry::make(Artist::ATTRIBUTE_SLUG)
                                    ->label(__('filament.fields.artist.slug.name')),

                                TextEntry::make('artistsong' . '.' . ArtistSong::ATTRIBUTE_AS)
                                    ->label(__('filament.fields.artist.songs.as.name')),
                            ])
                            ->columns(4),
                    ]),

                Section::make(__('filament.fields.base.timestamps'))
                    ->schema(parent::timestamps())
                    ->columns(3),
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

        if ($slug->isNotEmpty()) {
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
            RelationGroup::make(static::getLabel(),
                array_merge(
                    [
                        EntryThemeRelationManager::class,
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
     */
    public static function getFilters(): array
    {
        return array_merge(
            [
                SelectFilter::make(ThemeModel::ATTRIBUTE_TYPE)
                    ->label(__('filament.fields.anime_theme.type.name'))
                    ->options(ThemeType::asSelectArray()),

                NumberFilter::make(ThemeModel::ATTRIBUTE_SEQUENCE)
                    ->label(__('filament.fields.anime_theme.sequence.name')),
            ],
            parent::getFilters(),
        );
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array
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
     * @param  array|null  $actionsIncludedInGroup
     * @return array
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return array_merge(
            parent::getBulkActions(),
            [],
        );
    }

    /**
     * Get the table actions available for the resource.
     *
     * @return array
     */
    public static function getTableActions(): array
    {
        return array_merge(
            parent::getTableActions(),
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
            'index' => ListThemes::route('/'),
            'create' => CreateTheme::route('/create'),
            'view' => ViewTheme::route('/{record:theme_id}'),
            'edit' => EditTheme::route('/{record:theme_id}/edit'),
        ];
    }
}
