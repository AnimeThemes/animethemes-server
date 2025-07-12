<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\Theme;

use App\Enums\Models\Wiki\ThemeType;
use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\BelongsTo;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Filters\CheckboxFilter;
use App\Filament\Components\Filters\NumberFilter;
use App\Filament\Components\Filters\TextFilter;
use App\Filament\Components\Infolist\BelongsToEntry;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Anime as AnimeResource;
use App\Filament\Resources\Wiki\Anime\Theme as ThemeResource;
use App\Filament\Resources\Wiki\Anime\Theme\Entry\Pages\ListEntries;
use App\Filament\Resources\Wiki\Anime\Theme\Entry\Pages\ViewEntry;
use App\Filament\Resources\Wiki\Anime\Theme\Entry\RelationManagers\VideoEntryRelationManager;
use App\Filament\Resources\Wiki\Anime\Theme\RelationManagers\EntryThemeRelationManager;
use App\Models\Wiki\Anime\AnimeTheme as ThemeModel;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry as EntryModel;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\IconEntry;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Entry.
 */
class Entry extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = EntryModel::class;

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.anime_theme_entry');
    }

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.anime_theme_entries');
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
        return __('filament-icons.resources.anime_theme_entries');
    }

    /**
     * Get the title for the resource.
     *
     * @param  Model|null  $record
     * @return string|null
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRecordTitle(?Model $record): ?string
    {
        return $record instanceof EntryModel
            && $record->anime !== null
            && $record->animetheme !== null
            ? $record->getName()
            : null;
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
     * Get the URI key for the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRecordSlug(): string
    {
        return 'anime-theme-entries';
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
        return $query->with([AnimeThemeEntry::RELATION_ANIME_SHALLOW, AnimeThemeEntry::RELATION_THEME]);
    }

    /**
     * The form to the actions.
     *
     * @param  Schema  $schema
     * @return Schema
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
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
            ])
            ->searchable();
    }

    /**
     * Get the infolist available for the resource.
     *
     * @param  Schema  $schema
     * @return Schema
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
                    ->schema([
                        BelongsToEntry::make(EntryModel::RELATION_ANIME_SHALLOW, AnimeResource::class),

                        BelongsToEntry::make(EntryModel::RELATION_THEME, ThemeResource::class, true),

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
     * Get the relationships available for the resource.
     *
     * @return array<int, RelationGroup|class-string<\Filament\Resources\RelationManagers\RelationManager>>
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRelations(): array
    {
        return [
            RelationGroup::make(static::getModelLabel(), [
                VideoEntryRelationManager::class,

                ...parent::getBaseRelations(),
            ]),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array<int, \Filament\Tables\Filters\BaseFilter>
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
     * Get the pages available for the resource.
     *
     * @return array<string, \Filament\Resources\Pages\PageRegistration>
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getPages(): array
    {
        return [
            'index' => ListEntries::route('/'),
            'view' => ViewEntry::route('/{record:entry_id}'),
        ];
    }
}
