<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime;

use App\Enums\Filament\NavigationGroup;
use App\Enums\Models\Wiki\ThemeType;
use App\Filament\Actions\Models\Wiki\Song\LoadArtistsAction;
use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\BelongsTo;
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
use App\Filament\Resources\Wiki\Anime\Theme\Schemas\ThemeForm;
use App\Filament\Resources\Wiki\Artist as ArtistResource;
use App\Filament\Resources\Wiki\Group as GroupResource;
use App\Filament\Resources\Wiki\Song as SongResource;
use App\Filament\Resources\Wiki\Song\Performance\Schemas\PerformanceForm;
use App\Filament\Resources\Wiki\Song\RelationManagers\ThemeSongRelationManager;
use App\Models\Wiki\Anime\AnimeTheme as ThemeModel;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Models\Wiki\Song\Membership;
use Filament\Forms\Components\Repeater;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\QueryBuilder\Constraints\NumberConstraint;
use Filament\QueryBuilder\Constraints\SelectConstraint;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class Theme extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = ThemeModel::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.anime_theme');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.anime_themes');
    }

    public static function getNavigationGroup(): NavigationGroup
    {
        return NavigationGroup::CONTENT;
    }

    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::OutlinedListBullet;
    }

    /**
     * Get the title for the resource.
     */
    public static function getRecordTitle(?Model $record): ?string
    {
        return $record instanceof ThemeModel ? $record->anime->getName().' '.$record->getName() : null;
    }

    public static function canGloballySearch(): bool
    {
        return true;
    }

    public static function getRecordSlug(): string
    {
        return 'anime-themes';
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Necessary to prevent lazy loading when loading related resources
        /** @phpstan-ignore-next-line */
        return $query->with([
            ThemeModel::RELATION_ANIME,
            ThemeModel::RELATION_GROUP,
            ThemeModel::RELATION_ENTRIES,
            ThemeModel::RELATION_PERFORMANCES,
            ThemeModel::RELATION_SONG,
            'song.animethemes',
            ThemeModel::RELATION_PERFORMANCES_ARTISTS => function (MorphTo $morphTo): void {
                $morphTo->morphWith([
                    Artist::class => [],
                    Membership::class => [Membership::RELATION_GROUP, Membership::RELATION_MEMBER],
                ]);
            },
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('theme')
                            ->label(__('filament.resources.singularLabel.anime_theme'))
                            ->schema([
                                BelongsTo::make(ThemeModel::ATTRIBUTE_ANIME)
                                    ->resource(AnimeResource::class)
                                    ->hiddenOn(ThemeRelationManager::class)
                                    ->required(),

                                ThemeForm::typeField(),
                                ThemeForm::sequenceField(),

                                BelongsTo::make(ThemeModel::ATTRIBUTE_GROUP)
                                    ->resource(GroupResource::class)
                                    ->showCreateOption()
                                    ->live(),
                            ]),

                        Tab::make('song')
                            ->label(__('filament.resources.singularLabel.song'))
                            ->schema([
                                BelongsTo::make(ThemeModel::ATTRIBUTE_SONG)
                                    ->resource(SongResource::class)
                                    ->showCreateOption()
                                    ->live()
                                    ->hintAction(LoadArtistsAction::make()),

                                ...PerformanceForm::performancesFields(),
                            ]),

                        Tab::make('entries')
                            ->label(__('filament.resources.label.anime_theme_entries'))
                            ->schema([
                                Repeater::make(ThemeModel::RELATION_ENTRIES)
                                    ->label(__('filament.resources.label.anime_theme_entries'))
                                    ->addActionLabel(__('filament.buttons.add', ['label' => __('filament.resources.singularLabel.anime_theme_entry')]))
                                    ->relationship()
                                    ->schema(Entry::form($schema)->getComponents()),
                            ]),
                    ]),
            ])
            ->columns(1);
    }

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
                    ->formatStateUsing(fn (ThemeType $state): ?string => $state->localize()),

                TextColumn::make(ThemeModel::ATTRIBUTE_SEQUENCE)
                    ->label(__('filament.fields.anime_theme.sequence.name')),

                TextColumn::make(ThemeModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.anime_theme.slug.name')),

                BelongsToColumn::make(ThemeModel::RELATION_GROUP, GroupResource::class),

                BelongsToColumn::make(ThemeModel::RELATION_SONG, SongResource::class)
                    ->hiddenOn(ThemeSongRelationManager::class)
                    ->searchable(true, function (Builder $query, string $search): void {
                        $songs = Song::search($search)->take(25)->keys();

                        $query->whereHas(ThemeModel::RELATION_SONG, function (Builder $query) use ($songs): void {
                            $query->whereIn(Song::ATTRIBUTE_ID, $songs);
                        });
                    }, true),
            ])
            ->searchable();
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
                    ->schema([
                        TextEntry::make(ThemeModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        BelongsToEntry::make(ThemeModel::RELATION_ANIME, AnimeResource::class),

                        TextEntry::make(ThemeModel::ATTRIBUTE_TYPE)
                            ->label(__('filament.fields.anime_theme.type.name'))
                            ->formatStateUsing(fn (ThemeType $state): ?string => $state->localize()),

                        TextEntry::make(ThemeModel::ATTRIBUTE_SEQUENCE)
                            ->label(__('filament.fields.anime_theme.sequence.name')),

                        TextEntry::make(ThemeModel::ATTRIBUTE_SLUG)
                            ->label(__('filament.fields.anime_theme.slug.name')),

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
                            ->schema(ArtistResource::infolist($schema)->getComponents())
                            ->columnSpanFull(),
                    ]),

                TimestampSection::make(),
            ]);
    }

    /**
     * @return \Filament\Tables\Filters\BaseFilter[]
     */
    public static function getFilters(): array
    {
        return [
            QueryBuilder::make()
                ->constraints([
                    SelectConstraint::make(ThemeModel::ATTRIBUTE_TYPE)
                        ->label(__('filament.fields.anime_theme.type.name'))
                        ->options(ThemeType::class)
                        ->multiple(),

                    NumberConstraint::make(ThemeModel::ATTRIBUTE_SEQUENCE)
                        ->label(__('filament.fields.anime_theme.sequence.name')),

                    ...parent::getConstraints(),
                ]),

            Filter::make(ThemeType::IN->localize())
                ->label(__('filament.filters.anime_theme.without_in'))
                ->query(fn (Builder $query) => $query->whereNot(ThemeModel::ATTRIBUTE_TYPE, ThemeType::IN->value))
                ->default(true),

            ...parent::getFilters(),
        ];
    }

    /**
     * @return array<int, RelationGroup|class-string<\Filament\Resources\RelationManagers\RelationManager>>
     */
    public static function getRelations(): array
    {
        return [
            RelationGroup::make(static::getModelLabel(), [
                EntryThemeRelationManager::class,

                ...parent::getBaseRelations(),
            ]),
        ];
    }

    /**
     * @return array<string, \Filament\Resources\Pages\PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListThemes::route('/'),
            'view' => ViewTheme::route('/{record:theme_id}'),
        ];
    }
}
