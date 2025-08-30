<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\Theme;

use App\Actions\Models\Wiki\AttachResourceAction;
use App\Enums\Models\Wiki\ResourceSite;
use App\Enums\Models\Wiki\ThemeType;
use App\Filament\Actions\Models\Wiki\Anime\Theme\Entry\AttachEntryResourceAction;
use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\BelongsTo;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Fields\TextInput;
use App\Filament\Components\Filters\CheckboxFilter;
use App\Filament\Components\Filters\NumberFilter;
use App\Filament\Components\Filters\TextFilter;
use App\Filament\Components\Infolist\BelongsToEntry;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\RelationManagers\Wiki\ResourceRelationManager;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Anime as AnimeResource;
use App\Filament\Resources\Wiki\Anime\Theme as ThemeResource;
use App\Filament\Resources\Wiki\Anime\Theme\Entry\Pages\ListEntries;
use App\Filament\Resources\Wiki\Anime\Theme\Entry\Pages\ViewEntry;
use App\Filament\Resources\Wiki\Anime\Theme\Entry\RelationManagers\VideoEntryRelationManager;
use App\Filament\Resources\Wiki\Anime\Theme\RelationManagers\EntryThemeRelationManager;
use App\Filament\Resources\Wiki\Song as SongResource;
use App\Models\Wiki\Anime\AnimeTheme as ThemeModel;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry as EntryModel;
use App\Models\Wiki\Song;
use App\Rules\Wiki\Resource\AnimeThemeEntryResourceLinkFormatRule;
use Filament\Forms\Components\Checkbox;
use Filament\Infolists\Components\IconEntry;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Uri;

class Entry extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = EntryModel::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.anime_theme_entry');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.anime_theme_entries');
    }

    public static function getNavigationGroup(): string
    {
        return __('filament.resources.group.wiki');
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
        return $record instanceof EntryModel
            && $record->anime !== null
            && $record->animetheme !== null
            ? $record->getName()
            : null;
    }

    public static function canGloballySearch(): bool
    {
        return true;
    }

    public static function getRecordSlug(): string
    {
        return 'anime-theme-entries';
    }

    /**
     * @return Builder
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Necessary to prevent lazy loading when loading related resources
        return $query->with([
            AnimeThemeEntry::RELATION_ANIME_SHALLOW,
            AnimeThemeEntry::RELATION_SONG_SHALLOW,
            AnimeThemeEntry::RELATION_THEME,
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                BelongsTo::make(EntryModel::RELATION_THEME.'.'.ThemeModel::ATTRIBUTE_ANIME)
                    ->resource(AnimeResource::class, EntryModel::RELATION_ANIME_SHALLOW)
                    ->live(true)
                    ->required()
                    ->visibleOn([ListEntries::class, ViewEntry::class])
                    ->saveRelationshipsUsing(fn (EntryModel $record, $state) => $record->animetheme->anime()->associate(intval($state))->save()),

                Select::make(EntryModel::ATTRIBUTE_THEME)
                    ->label(__('filament.resources.singularLabel.anime_theme'))
                    ->relationship(EntryModel::RELATION_THEME, ThemeModel::ATTRIBUTE_ID)
                    ->required()
                    ->visibleOn([ListEntries::class, ViewEntry::class])
                    ->options(function (Get $get) {
                        return ThemeModel::query()
                            ->where(ThemeModel::ATTRIBUTE_ANIME, $get(EntryModel::RELATION_THEME.'.'.ThemeModel::ATTRIBUTE_ANIME))
                            ->get()
                            ->mapWithKeys(fn (ThemeModel $theme) => [$theme->getKey() => $theme->getName()])
                            ->toArray();
                    }),

                TextInput::make(EntryModel::ATTRIBUTE_VERSION)
                    ->label(__('filament.fields.anime_theme_entry.version.name'))
                    ->helperText(__('filament.fields.anime_theme_entry.version.help'))
                    ->integer(),

                TextInput::make(EntryModel::ATTRIBUTE_EPISODES)
                    ->label(__('filament.fields.anime_theme_entry.episodes.name'))
                    ->helperText(__('filament.fields.anime_theme_entry.episodes.help'))
                    ->maxLength(192),

                Checkbox::make(EntryModel::ATTRIBUTE_NSFW)
                    ->label(__('filament.fields.anime_theme_entry.nsfw.name'))
                    ->helperText(__('filament.fields.anime_theme_entry.nsfw.help'))
                    ->rules(['boolean']),

                Checkbox::make(EntryModel::ATTRIBUTE_SPOILER)
                    ->label(__('filament.fields.anime_theme_entry.spoiler.name'))
                    ->helperText(__('filament.fields.anime_theme_entry.spoiler.help'))
                    ->rules(['boolean']),

                TextInput::make(EntryModel::ATTRIBUTE_NOTES)
                    ->label(__('filament.fields.anime_theme_entry.notes.name'))
                    ->helperText(__('filament.fields.anime_theme_entry.notes.help'))
                    ->maxLength(192),

                TextInput::make(ResourceSite::YOUTUBE->name)
                    ->label(ResourceSite::YOUTUBE->localize())
                    ->helperText(__('filament.fields.anime_theme_entry.youtube.help'))
                    ->url()
                    ->maxLength(255)
                    ->rule(new AnimeThemeEntryResourceLinkFormatRule(ResourceSite::YOUTUBE))
                    ->uri()
                    ->saveRelationshipsUsing(function (EntryModel $record, AttachResourceAction $action, ?Uri $state) {
                        $fields = [
                            ResourceSite::YOUTUBE->name => $state,
                        ];

                        $action->handle($record, $fields, [ResourceSite::YOUTUBE]);
                    }),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                BelongsToColumn::make(EntryModel::RELATION_ANIME_SHALLOW, AnimeResource::class),

                BelongsToColumn::make(EntryModel::RELATION_THEME, ThemeResource::class, true)
                    ->hiddenOn(EntryThemeRelationManager::class),

                TextColumn::make(EntryModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(EntryModel::ATTRIBUTE_VERSION)
                    ->label(__('filament.fields.anime_theme_entry.version.name')),

                TextColumn::make(EntryModel::ATTRIBUTE_EPISODES)
                    ->label(__('filament.fields.anime_theme_entry.episodes.name')),

                IconColumn::make(EntryModel::ATTRIBUTE_NSFW)
                    ->label(__('filament.fields.anime_theme_entry.nsfw.name'))
                    ->boolean(),

                IconColumn::make(EntryModel::ATTRIBUTE_SPOILER)
                    ->label(__('filament.fields.anime_theme_entry.spoiler.name'))
                    ->boolean(),

                TextColumn::make(EntryModel::ATTRIBUTE_NOTES)
                    ->label(__('filament.fields.anime_theme_entry.notes.name'))
                    ->limit(50)
                    ->tooltip(fn (TextColumn $column) => $column->getState()),

                BelongsToColumn::make(EntryModel::RELATION_SONG_SHALLOW, SongResource::class)
                    ->hiddenOn(EntryThemeRelationManager::class)
                    ->searchable(true, function (Builder $query, string $search) {
                        $songs = Song::search($search)->take(25)->keys();

                        $query->whereHas(EntryModel::RELATION_SONG, function (Builder $query) use ($songs) {
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
                        BelongsToEntry::make(EntryModel::RELATION_ANIME_SHALLOW, AnimeResource::class),

                        BelongsToEntry::make(EntryModel::RELATION_THEME, ThemeResource::class, true),

                        BelongsToEntry::make(EntryModel::RELATION_SONG, SongResource::class, true),

                        TextEntry::make(EntryModel::ATTRIBUTE_VERSION)
                            ->label(__('filament.fields.anime_theme_entry.version.name')),

                        TextEntry::make(EntryModel::ATTRIBUTE_EPISODES)
                            ->label(__('filament.fields.anime_theme_entry.episodes.name')),

                        TextEntry::make(EntryModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        IconEntry::make(EntryModel::ATTRIBUTE_NSFW)
                            ->label(__('filament.fields.anime_theme_entry.nsfw.name'))
                            ->boolean(),

                        IconEntry::make(EntryModel::ATTRIBUTE_SPOILER)
                            ->label(__('filament.fields.anime_theme_entry.spoiler.name'))
                            ->boolean(),

                        TextEntry::make(EntryModel::ATTRIBUTE_NOTES)
                            ->label(__('filament.fields.anime_theme_entry.notes.name'))
                            ->columnSpanFull(),
                    ])
                    ->columns(4),

                TimestampSection::make(),
            ]);
    }

    /**
     * @return array<int, RelationGroup|class-string<\Filament\Resources\RelationManagers\RelationManager>>
     */
    public static function getRelations(): array
    {
        return [
            RelationGroup::make(static::getModelLabel(), [
                VideoEntryRelationManager::class,
                ResourceRelationManager::class,

                ...parent::getBaseRelations(),
            ]),
        ];
    }

    /**
     * @return \Filament\Tables\Filters\BaseFilter[]
     */
    public static function getFilters(): array
    {
        return [
            NumberFilter::make(EntryModel::ATTRIBUTE_VERSION)
                ->label(__('filament.fields.anime_theme_entry.version.name')),

            TextFilter::make(EntryModel::ATTRIBUTE_EPISODES)
                ->label(__('filament.fields.anime_theme_entry.episodes.name')),

            CheckboxFilter::make(EntryModel::ATTRIBUTE_NSFW)
                ->label(__('filament.fields.anime_theme_entry.nsfw.name')),

            CheckboxFilter::make(EntryModel::ATTRIBUTE_SPOILER)
                ->label(__('filament.fields.anime_theme_entry.spoiler.name')),

            Filter::make(ThemeType::IN->localize())
                ->label(__('filament.filters.anime_theme.without_in'))
                ->query(fn (Builder $query) => $query->whereDoesntHaveRelation(AnimeThemeEntry::RELATION_THEME, ThemeModel::ATTRIBUTE_TYPE, ThemeType::IN->value))
                ->default(true),

            ...parent::getFilters(),
        ];
    }

    /**
     * @return array<int, \Filament\Actions\Action|\Filament\Actions\ActionGroup>
     */
    public static function getRecordActions(): array
    {
        return [
            AttachEntryResourceAction::make(),
        ];
    }

    /**
     * @return array<string, \Filament\Resources\Pages\PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListEntries::route('/'),
            'view' => ViewEntry::route('/{record:entry_id}'),
        ];
    }
}
