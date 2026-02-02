<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\Theme;

use App\Actions\Models\Wiki\AttachResourceAction;
use App\Enums\Filament\NavigationGroup;
use App\Enums\Models\Wiki\ResourceSite;
use App\Enums\Models\Wiki\ThemeType;
use App\Filament\Actions\Models\Wiki\Anime\Theme\Entry\AttachEntryResourceAction;
use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\BelongsTo;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Fields\TextInput;
use App\Filament\Components\Infolist\BelongsToEntry;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\RelationManagers\Wiki\ResourceRelationManager;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Anime\Theme\Entry\Pages\ListEntries;
use App\Filament\Resources\Wiki\Anime\Theme\Entry\Pages\ViewEntry;
use App\Filament\Resources\Wiki\Anime\Theme\Entry\RelationManagers\VideoEntryRelationManager;
use App\Filament\Resources\Wiki\Anime\Theme\RelationManagers\EntryThemeRelationManager;
use App\Filament\Resources\Wiki\Anime\ThemeResource;
use App\Filament\Resources\Wiki\AnimeResource;
use App\Filament\Resources\Wiki\SongResource;
use App\Filament\Submission\Resources\Anime\Pages\CreateAnimeSubmission;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Song;
use App\Rules\Wiki\Resource\AnimeThemeEntryResourceLinkFormatRule;
use Filament\Forms\Components\Checkbox;
use Filament\Infolists\Components\IconEntry;
use Filament\QueryBuilder\Constraints\BooleanConstraint;
use Filament\QueryBuilder\Constraints\NumberConstraint;
use Filament\QueryBuilder\Constraints\TextConstraint;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Uri;

class EntryResource extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = AnimeThemeEntry::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.anime_theme_entry');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.anime_theme_entries');
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
        return $record instanceof AnimeThemeEntry
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
                BelongsTo::make(AnimeThemeEntry::RELATION_THEME.'.'.AnimeTheme::ATTRIBUTE_ANIME)
                    ->resource(AnimeResource::class, AnimeThemeEntry::RELATION_ANIME_SHALLOW)
                    ->live(true)
                    ->required()
                    ->visibleOn([ListEntries::class, ViewEntry::class])
                    ->saveRelationshipsUsing(fn (AnimeThemeEntry $record, $state) => $record->animetheme->anime()->associate(intval($state))->save()),

                Select::make(AnimeThemeEntry::ATTRIBUTE_THEME)
                    ->label(__('filament.resources.singularLabel.anime_theme'))
                    ->relationship(AnimeThemeEntry::RELATION_THEME, AnimeTheme::ATTRIBUTE_ID)
                    ->required()
                    ->visibleOn([ListEntries::class, ViewEntry::class])
                    ->options(fn (Get $get) => AnimeTheme::query()
                        ->where(AnimeTheme::ATTRIBUTE_ANIME, $get(AnimeThemeEntry::RELATION_THEME.'.'.AnimeTheme::ATTRIBUTE_ANIME))
                        ->get()
                        ->mapWithKeys(fn (AnimeTheme $theme): array => [$theme->getKey() => $theme->getName()])
                        ->toArray()),

                TextInput::make(AnimeThemeEntry::ATTRIBUTE_VERSION)
                    ->label(__('filament.fields.anime_theme_entry.version.name'))
                    ->helperText(__('filament.fields.anime_theme_entry.version.help'))
                    ->default(1)
                    ->integer()
                    ->required(),

                TextInput::make(AnimeThemeEntry::ATTRIBUTE_EPISODES)
                    ->label(__('filament.fields.anime_theme_entry.episodes.name'))
                    ->helperText(__('filament.fields.anime_theme_entry.episodes.help'))
                    ->maxLength(192),

                Checkbox::make(AnimeThemeEntry::ATTRIBUTE_NSFW)
                    ->label(__('filament.fields.anime_theme_entry.nsfw.name'))
                    ->helperText(__('filament.fields.anime_theme_entry.nsfw.help')),

                Checkbox::make(AnimeThemeEntry::ATTRIBUTE_SPOILER)
                    ->label(__('filament.fields.anime_theme_entry.spoiler.name'))
                    ->helperText(__('filament.fields.anime_theme_entry.spoiler.help')),

                TextInput::make(AnimeThemeEntry::ATTRIBUTE_NOTES)
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
                    ->saveRelationshipsUsing(function (AnimeThemeEntry $record, AttachResourceAction $action, ?Uri $state, $livewire): void {
                        if ($livewire instanceof CreateAnimeSubmission) {
                            return;
                        }

                        $action->handle($record, [ResourceSite::YOUTUBE->name => $state], [ResourceSite::YOUTUBE]);
                    }),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                BelongsToColumn::make(AnimeThemeEntry::RELATION_ANIME_SHALLOW, AnimeResource::class),

                BelongsToColumn::make(AnimeThemeEntry::RELATION_THEME, ThemeResource::class, true)
                    ->hiddenOn(EntryThemeRelationManager::class),

                TextColumn::make(AnimeThemeEntry::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(AnimeThemeEntry::ATTRIBUTE_VERSION)
                    ->label(__('filament.fields.anime_theme_entry.version.name')),

                TextColumn::make(AnimeThemeEntry::ATTRIBUTE_EPISODES)
                    ->label(__('filament.fields.anime_theme_entry.episodes.name')),

                IconColumn::make(AnimeThemeEntry::ATTRIBUTE_NSFW)
                    ->label(__('filament.fields.anime_theme_entry.nsfw.name'))
                    ->boolean(),

                IconColumn::make(AnimeThemeEntry::ATTRIBUTE_SPOILER)
                    ->label(__('filament.fields.anime_theme_entry.spoiler.name'))
                    ->boolean(),

                TextColumn::make(AnimeThemeEntry::ATTRIBUTE_NOTES)
                    ->label(__('filament.fields.anime_theme_entry.notes.name'))
                    ->limit(50)
                    ->tooltip(fn (TextColumn $column): mixed => $column->getState()),

                BelongsToColumn::make(AnimeThemeEntry::RELATION_SONG_SHALLOW, SongResource::class)
                    ->hiddenOn(EntryThemeRelationManager::class)
                    ->searchable(true, function (Builder $query, string $search): void {
                        $songs = Song::search($search)->take(25)->keys();

                        $query->whereHas(AnimeThemeEntry::RELATION_SONG, function (Builder $query) use ($songs): void {
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
                        BelongsToEntry::make(AnimeThemeEntry::RELATION_ANIME_SHALLOW, AnimeResource::class),

                        BelongsToEntry::make(AnimeThemeEntry::RELATION_THEME, ThemeResource::class, true),

                        BelongsToEntry::make(AnimeThemeEntry::RELATION_SONG, SongResource::class, true),

                        TextEntry::make(AnimeThemeEntry::ATTRIBUTE_VERSION)
                            ->label(__('filament.fields.anime_theme_entry.version.name')),

                        TextEntry::make(AnimeThemeEntry::ATTRIBUTE_EPISODES)
                            ->label(__('filament.fields.anime_theme_entry.episodes.name')),

                        TextEntry::make(AnimeThemeEntry::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        IconEntry::make(AnimeThemeEntry::ATTRIBUTE_NSFW)
                            ->label(__('filament.fields.anime_theme_entry.nsfw.name'))
                            ->boolean(),

                        IconEntry::make(AnimeThemeEntry::ATTRIBUTE_SPOILER)
                            ->label(__('filament.fields.anime_theme_entry.spoiler.name'))
                            ->boolean(),

                        TextEntry::make(AnimeThemeEntry::ATTRIBUTE_NOTES)
                            ->label(__('filament.fields.anime_theme_entry.notes.name'))
                            ->columnSpanFull(),
                    ])
                    ->columns(4),

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
                    NumberConstraint::make(AnimeThemeEntry::ATTRIBUTE_VERSION)
                        ->label(__('filament.fields.anime_theme_entry.version.name')),

                    TextConstraint::make(AnimeThemeEntry::ATTRIBUTE_EPISODES)
                        ->label(__('filament.fields.anime_theme_entry.episodes.name')),

                    BooleanConstraint::make(AnimeThemeEntry::ATTRIBUTE_NSFW)
                        ->label(__('filament.fields.anime_theme_entry.nsfw.name')),

                    BooleanConstraint::make(AnimeThemeEntry::ATTRIBUTE_SPOILER)
                        ->label(__('filament.fields.anime_theme_entry.spoiler.name')),

                    TextConstraint::make(AnimeThemeEntry::ATTRIBUTE_NOTES)
                        ->label(__('filament.fields.anime_theme_entry.notes.name')),

                    ...parent::getConstraints(),
                ]),

            Filter::make(ThemeType::IN->localize())
                ->label(__('filament.filters.anime_theme.without_in'))
                ->query(fn (Builder $query) => $query->whereDoesntHaveRelation(AnimeThemeEntry::RELATION_THEME, AnimeTheme::ATTRIBUTE_TYPE, ThemeType::IN->value))
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
                VideoEntryRelationManager::class,
                ResourceRelationManager::class,

                ...parent::getBaseRelations(),
            ]),
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
