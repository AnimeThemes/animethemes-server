<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\Theme;

use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Anime as AnimeResource;
use App\Filament\Resources\Wiki\Anime\Theme as ThemeResource;
use App\Filament\Resources\Wiki\Anime\Theme\Entry\Pages\CreateEntry;
use App\Filament\Resources\Wiki\Anime\Theme\Entry\Pages\EditEntry;
use App\Filament\Resources\Wiki\Anime\Theme\Entry\Pages\ListEntries;
use App\Filament\Resources\Wiki\Anime\Theme\Entry\Pages\ViewEntry;
use App\Filament\Resources\Wiki\Anime\Theme\Entry\RelationManagers\VideoEntryRelationManager;
use App\Models\Wiki\Anime as AnimeModel;
use App\Models\Wiki\Anime\AnimeTheme as ThemeModel;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry as EntryModel;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
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
     * Get the title for the resource.
     *
     * @return string|null
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRecordTitle(?Model $record): ?string
    {
        if ($record instanceof EntryModel) {
            $theme = $record->animetheme;
            $anime = $theme->anime->name;
            $text = $anime.' '.$theme->slug;

            if ($record->version !== null) {
                return $text.'v'.$record->version;
            }

            return $text;
        }

        return null;
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
        return [EntryModel::RELATION_ANIME.'.'.AnimeModel::ATTRIBUTE_NAME];
    }

    /**
     * Get the URI key for the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getSlug(): string
    {
        return 'anime-theme-entries';
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
        return EntryModel::ATTRIBUTE_ID;
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
                Select::make(EntryModel::RELATION_ANIME.'.'.AnimeModel::ATTRIBUTE_NAME)
                    ->label(__('filament.resources.singularLabel.anime'))
                    ->relationship(EntryModel::RELATION_ANIME, AnimeModel::ATTRIBUTE_NAME)
                    ->searchable()
                    ->createOptionForm(AnimeResource::form($form)->getComponents()),

                Select::make(EntryModel::ATTRIBUTE_THEME)
                    ->label(__('filament.resources.singularLabel.anime_theme'))
                    ->relationship(EntryModel::RELATION_THEME, ThemeModel::ATTRIBUTE_SLUG)
                    ->searchable()
                    ->createOptionForm(ThemeResource::form($form)->getComponents()),

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
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make(EntryModel::RELATION_ANIME.'.'.AnimeModel::ATTRIBUTE_NAME)
                    ->label(__('filament.resources.singularLabel.anime'))
                    ->urlToRelated(AnimeResource::class, EntryModel::RELATION_ANIME),

                TextColumn::make(EntryModel::RELATION_THEME.'.'.ThemeModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.resources.singularLabel.anime_theme'))
                    ->urlToRelated(ThemeResource::class, EntryModel::RELATION_THEME),

                TextColumn::make(EntryModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id'))
                    ->numeric()
                    ->sortable(),

                TextColumn::make(EntryModel::ATTRIBUTE_VERSION)
                    ->label(__('filament.fields.anime_theme_entry.version.name'))
                    ->numeric(),

                TextColumn::make(EntryModel::ATTRIBUTE_EPISODES)
                    ->label(__('filament.fields.anime_theme_entry.episodes.name')),

                CheckboxColumn::make(EntryModel::ATTRIBUTE_NSFW)
                    ->label(__('filament.fields.anime_theme_entry.nsfw.name')),

                CheckboxColumn::make(EntryModel::ATTRIBUTE_SPOILER)
                    ->label(__('filament.fields.anime_theme_entry.spoiler.name')),

                TextColumn::make(EntryModel::ATTRIBUTE_NOTES)
                    ->label(__('filament.fields.anime_theme_entry.notes.name')),
            ])
            ->defaultSort(EntryModel::ATTRIBUTE_ID, 'desc')
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
            VideoEntryRelationManager::class,
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
            []
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
            'index' => ListEntries::route('/'),
            'create' => CreateEntry::route('/create'),
            'view' => ViewEntry::route('/{record:entry_id}'),
            'edit' => EditEntry::route('/{record:entry_id}/edit'),
        ];
    }
}
