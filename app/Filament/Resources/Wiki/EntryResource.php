<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

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
use App\Filament\Resources\Wiki\Entry\Pages\ListEntries;
use App\Filament\Resources\Wiki\Entry\Pages\ViewEntry;
use App\Filament\Resources\Wiki\Entry\RelationManagers\VideoEntryRelationManager;
use App\Filament\Resources\Wiki\Theme\RelationManagers\EntryThemeRelationManager;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Song;
use App\Models\Wiki\Theme;
use App\Rules\Wiki\Resource\EntryResourceLinkFormatRule;
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
    protected static ?string $model = Entry::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.entry');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.entries');
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
        return $record instanceof Entry
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
            Entry::RELATION_ANIME_SHALLOW,
            Entry::RELATION_SONG_SHALLOW,
            Entry::RELATION_THEME,
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                BelongsTo::make(Entry::RELATION_THEME.'.'.Theme::ATTRIBUTE_ANIME)
                    ->resource(AnimeResource::class, Entry::RELATION_ANIME_SHALLOW)
                    ->live(true)
                    ->required()
                    ->visibleOn([ListEntries::class, ViewEntry::class])
                    ->saveRelationshipsUsing(fn (Entry $record, $state) => $record->animetheme->anime()->associate(intval($state))->save()),

                Select::make(Entry::ATTRIBUTE_THEME)
                    ->label(__('filament.resources.singularLabel.theme'))
                    ->relationship(Entry::RELATION_THEME, Theme::ATTRIBUTE_ID)
                    ->required()
                    ->visibleOn([ListEntries::class, ViewEntry::class])
                    ->options(fn (Get $get) => Theme::query()
                        ->where(Theme::ATTRIBUTE_ANIME, $get(Entry::RELATION_THEME.'.'.Theme::ATTRIBUTE_ANIME))
                        ->get()
                        ->mapWithKeys(fn (Theme $theme): array => [$theme->getKey() => $theme->slug])
                        ->toArray()),

                TextInput::make(Entry::ATTRIBUTE_VERSION)
                    ->label(__('filament.fields.entry.version.name'))
                    ->helperText(__('filament.fields.entry.version.help'))
                    ->default(1)
                    ->integer()
                    ->required(),

                TextInput::make(Entry::ATTRIBUTE_EPISODES)
                    ->label(__('filament.fields.entry.episodes.name'))
                    ->helperText(__('filament.fields.entry.episodes.help'))
                    ->maxLength(192),

                Checkbox::make(Entry::ATTRIBUTE_NSFW)
                    ->label(__('filament.fields.entry.nsfw.name'))
                    ->helperText(__('filament.fields.entry.nsfw.help')),

                Checkbox::make(Entry::ATTRIBUTE_SPOILER)
                    ->label(__('filament.fields.entry.spoiler.name'))
                    ->helperText(__('filament.fields.entry.spoiler.help')),

                TextInput::make(Entry::ATTRIBUTE_NOTES)
                    ->label(__('filament.fields.entry.notes.name'))
                    ->helperText(__('filament.fields.entry.notes.help'))
                    ->maxLength(192),

                TextInput::make(ResourceSite::YOUTUBE->name)
                    ->label(ResourceSite::YOUTUBE->localize())
                    ->helperText(__('filament.fields.entry.youtube.help'))
                    ->url()
                    ->maxLength(255)
                    ->rule(new EntryResourceLinkFormatRule(ResourceSite::YOUTUBE))
                    ->uri()
                    ->saveRelationshipsUsing(function (Entry $record, AttachResourceAction $action, ?Uri $state, $livewire): void {
                        $action->handle($record, [ResourceSite::YOUTUBE->name => $state], [ResourceSite::YOUTUBE]);
                    }),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                BelongsToColumn::make(Entry::RELATION_ANIME_SHALLOW, AnimeResource::class),

                BelongsToColumn::make(Entry::RELATION_THEME, ThemeResource::class)
                    ->hiddenOn(EntryThemeRelationManager::class)
                    ->formatStateUsing(fn (Entry $record) => $record->animetheme->slug)
                    ->tooltip(fn (Entry $record) => $record->animetheme->slug),

                TextColumn::make(Entry::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(Entry::ATTRIBUTE_VERSION)
                    ->label(__('filament.fields.entry.version.name')),

                TextColumn::make(Entry::ATTRIBUTE_EPISODES)
                    ->label(__('filament.fields.entry.episodes.name')),

                IconColumn::make(Entry::ATTRIBUTE_NSFW)
                    ->label(__('filament.fields.entry.nsfw.name'))
                    ->boolean(),

                IconColumn::make(Entry::ATTRIBUTE_SPOILER)
                    ->label(__('filament.fields.entry.spoiler.name'))
                    ->boolean(),

                TextColumn::make(Entry::ATTRIBUTE_NOTES)
                    ->label(__('filament.fields.entry.notes.name'))
                    ->limit(50)
                    ->tooltip(fn (string $state): string => $state),

                BelongsToColumn::make(Entry::RELATION_SONG_SHALLOW, SongResource::class)
                    ->hiddenOn(EntryThemeRelationManager::class)
                    ->searchable(true, function (Builder $query, string $search): void {
                        $songs = Song::search($search)->take(25)->keys();

                        $query->whereHas(Entry::RELATION_SONG, function (Builder $query) use ($songs): void {
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
                        BelongsToEntry::make(Entry::RELATION_ANIME_SHALLOW, AnimeResource::class),

                        BelongsToEntry::make(Entry::RELATION_THEME, ThemeResource::class)
                            ->formatStateUsing(fn (Entry $record) => $record->animetheme->slug)
                            ->tooltip(fn (Entry $record) => $record->animetheme->slug),

                        BelongsToEntry::make(Entry::RELATION_SONG, SongResource::class, true),

                        TextEntry::make(Entry::ATTRIBUTE_VERSION)
                            ->label(__('filament.fields.entry.version.name')),

                        TextEntry::make(Entry::ATTRIBUTE_EPISODES)
                            ->label(__('filament.fields.entry.episodes.name')),

                        TextEntry::make(Entry::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        IconEntry::make(Entry::ATTRIBUTE_NSFW)
                            ->label(__('filament.fields.entry.nsfw.name'))
                            ->boolean(),

                        IconEntry::make(Entry::ATTRIBUTE_SPOILER)
                            ->label(__('filament.fields.entry.spoiler.name'))
                            ->boolean(),

                        TextEntry::make(Entry::ATTRIBUTE_NOTES)
                            ->label(__('filament.fields.entry.notes.name'))
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
                    NumberConstraint::make(Entry::ATTRIBUTE_VERSION)
                        ->label(__('filament.fields.entry.version.name')),

                    TextConstraint::make(Entry::ATTRIBUTE_EPISODES)
                        ->label(__('filament.fields.entry.episodes.name')),

                    BooleanConstraint::make(Entry::ATTRIBUTE_NSFW)
                        ->label(__('filament.fields.entry.nsfw.name')),

                    BooleanConstraint::make(Entry::ATTRIBUTE_SPOILER)
                        ->label(__('filament.fields.entry.spoiler.name')),

                    TextConstraint::make(Entry::ATTRIBUTE_NOTES)
                        ->label(__('filament.fields.entry.notes.name')),

                    ...parent::getConstraints(),
                ]),

            Filter::make(ThemeType::IN->localize())
                ->label(__('filament.filters.theme.without_in'))
                ->query(fn (Builder $query) => $query->whereDoesntHaveRelation(Entry::RELATION_THEME, Theme::ATTRIBUTE_TYPE, ThemeType::IN->value))
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
