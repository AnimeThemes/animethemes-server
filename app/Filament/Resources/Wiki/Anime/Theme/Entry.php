<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\Theme;

use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\BelongsTo;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Filters\CheckboxFilter;
use App\Filament\Components\Filters\NumberFilter;
use App\Filament\Components\Filters\TextFilter;
use App\Filament\Components\Infolist\BelongsToEntry;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Anime as AnimeResource;
use App\Filament\Resources\Wiki\Anime\Theme as ThemeResource;
use App\Filament\Resources\Wiki\Anime\Theme\Entry\Pages\CreateEntry;
use App\Filament\Resources\Wiki\Anime\Theme\Entry\Pages\EditEntry;
use App\Filament\Resources\Wiki\Anime\Theme\Entry\Pages\ListEntries;
use App\Filament\Resources\Wiki\Anime\Theme\Entry\Pages\ViewEntry;
use App\Filament\Resources\Wiki\Anime\Theme\Entry\RelationManagers\VideoEntryRelationManager;
use App\Filament\Resources\Wiki\Anime\Theme\RelationManagers\EntryThemeRelationManager;
use App\Models\Wiki\Anime\AnimeTheme as ThemeModel;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry as EntryModel;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Tables\Columns\IconColumn;
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
     * @var string|null
     */
    protected static ?string $model = EntryModel::class;

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getLabel(): string
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
    public static function getPluralLabel(): string
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
     * @param Model|null $record
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
     * @param  Form  $form
     * @return Form
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                BelongsTo::make(EntryModel::RELATION_THEME . '.' . ThemeModel::ATTRIBUTE_ANIME)
                    ->resource(AnimeResource::class, EntryModel::RELATION_ANIME_SHALLOW)
                    ->live(true)
                    ->required()
                    ->rules(['required'])
                    ->visibleOn([CreateEntry::class, EditEntry::class])
                    ->saveRelationshipsUsing(fn (EntryModel $record, $state) => $record->animetheme->anime()->associate(intval($state))->save()),

                Select::make(EntryModel::ATTRIBUTE_THEME)
                    ->label(__('filament.resources.singularLabel.anime_theme'))
                    ->relationship(EntryModel::RELATION_THEME, ThemeModel::ATTRIBUTE_ID)
                    ->required()
                    ->rules(['required'])
                    ->visibleOn([CreateEntry::class, EditEntry::class])
                    ->options(function (Get $get) {
                        return ThemeModel::query()
                            ->where(ThemeModel::ATTRIBUTE_ANIME, $get(EntryModel::RELATION_THEME . '.' . ThemeModel::ATTRIBUTE_ANIME))
                            ->get()
                            ->mapWithKeys(fn (ThemeModel $theme) => [$theme->getKey() => $theme->getName()])
                            ->toArray();
                    }),

                TextInput::make(EntryModel::ATTRIBUTE_VERSION)
                    ->label(__('filament.fields.anime_theme_entry.version.name'))
                    ->helperText(__('filament.fields.anime_theme_entry.version.help'))
                    ->numeric()
                    ->rules(['nullable', 'integer']),

                TextInput::make(EntryModel::ATTRIBUTE_EPISODES)
                    ->label(__('filament.fields.anime_theme_entry.episodes.name'))
                    ->helperText(__('filament.fields.anime_theme_entry.episodes.help'))
                    ->maxLength(192)
                    ->rules(['nullable', 'max:192']),

                Checkbox::make(EntryModel::ATTRIBUTE_NSFW)
                    ->label(__('filament.fields.anime_theme_entry.nsfw.name'))
                    ->helperText(__('filament.fields.anime_theme_entry.nsfw.help'))
                    ->rules(['nullable', 'boolean']),

                Checkbox::make(EntryModel::ATTRIBUTE_SPOILER)
                    ->label(__('filament.fields.anime_theme_entry.spoiler.name'))
                    ->helperText(__('filament.fields.anime_theme_entry.spoiler.help'))
                    ->rules(['nullable', 'boolean']),

                TextInput::make(EntryModel::ATTRIBUTE_NOTES)
                    ->label(__('filament.fields.anime_theme_entry.notes.name'))
                    ->helperText(__('filament.fields.anime_theme_entry.notes.help'))
                    ->maxLength(192)
                    ->rules(['nullable', 'max:192']),
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
                    ->label(__('filament.fields.anime_theme_entry.notes.name')),
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
                        BelongsToEntry::make(EntryModel::RELATION_ANIME_SHALLOW, AnimeResource::class),

                        BelongsToEntry::make(EntryModel::RELATION_THEME, ThemeResource::class, true),

                        TextEntry::make(EntryModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(EntryModel::ATTRIBUTE_VERSION)
                            ->label(__('filament.fields.anime_theme_entry.version.name')),

                        TextEntry::make(EntryModel::ATTRIBUTE_EPISODES)
                            ->label(__('filament.fields.anime_theme_entry.episodes.name')),

                        IconEntry::make(EntryModel::ATTRIBUTE_NSFW)
                            ->label(__('filament.fields.anime_theme_entry.nsfw.name'))
                            ->boolean(),

                        IconEntry::make(EntryModel::ATTRIBUTE_SPOILER)
                            ->label(__('filament.fields.anime_theme_entry.spoiler.name'))
                            ->boolean(),

                        TextEntry::make(EntryModel::ATTRIBUTE_NOTES)
                            ->label(__('filament.fields.anime_theme_entry.notes.name')),
                    ])
                    ->columns(2),

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
                        VideoEntryRelationManager::class,
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
                NumberFilter::make(EntryModel::ATTRIBUTE_VERSION)
                    ->label(__('filament.fields.anime_theme_entry.version.name')),

                TextFilter::make(EntryModel::ATTRIBUTE_EPISODES)
                    ->label(__('filament.fields.anime_theme_entry.episodes.name')),

                CheckboxFilter::make(EntryModel::ATTRIBUTE_NSFW)
                    ->label(__('filament.fields.anime_theme_entry.nsfw.name')),

                CheckboxFilter::make(EntryModel::ATTRIBUTE_SPOILER)
                    ->label(__('filament.fields.anime_theme_entry.spoiler.name')),
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
            'index' => ListEntries::route('/'),
            'create' => CreateEntry::route('/create'),
            'view' => ViewEntry::route('/{record:entry_id}'),
            'edit' => EditEntry::route('/{record:entry_id}/edit'),
        ];
    }
}
