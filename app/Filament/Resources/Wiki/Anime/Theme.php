<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime;

use App\Enums\Models\Wiki\ThemeType;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Filters\NumberFilter;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Resources\BaseRelationManager;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Anime as AnimeResource;
use App\Filament\Resources\Wiki\Anime\Theme\Pages\CreateTheme;
use App\Filament\Resources\Wiki\Anime\Theme\Pages\EditTheme;
use App\Filament\Resources\Wiki\Anime\Theme\Pages\ListThemes;
use App\Filament\Resources\Wiki\Anime\Theme\Pages\ViewTheme;
use App\Filament\Resources\Wiki\Anime\Theme\RelationManagers\EntryThemeRelationManager;
use App\Filament\Resources\Wiki\Group as GroupResource;
use App\Filament\Resources\Wiki\Song as SongResource;
use App\Models\Wiki\Anime as AnimeModel;
use App\Models\Wiki\Anime\AnimeTheme as ThemeModel;
use App\Models\Wiki\Group;
use App\Models\Wiki\Song;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
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
     * @return string|null
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRecordTitle(?Model $record): ?string
    {
        return $record instanceof ThemeModel ? $record->anime->getName().' '.$record->slug : null;
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
    public static function getSlug(): string
    {
        return static::getDefaultSlug().'anime-themes';
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
        return ThemeModel::ATTRIBUTE_ID;
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
                Select::make(ThemeModel::ATTRIBUTE_ANIME)
                    ->label(__('filament.resources.singularLabel.anime'))
                    ->relationship(ThemeModel::RELATION_ANIME, AnimeModel::ATTRIBUTE_NAME)
                    ->searchable()
                    ->hiddenOn(BaseRelationManager::class),

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

                Select::make(ThemeModel::ATTRIBUTE_GROUP)
                    ->label(__('filament.resources.singularLabel.group'))
                    ->relationship(ThemeModel::RELATION_GROUP, Group::ATTRIBUTE_NAME)
                    ->searchable()
                    ->live(true)
                    ->afterStateUpdated(fn (Set $set, Get $get) => Theme::setThemeSlug($set, $get))
                    ->createOptionForm(GroupResource::form($form)->getComponents()),

                Select::make(ThemeModel::ATTRIBUTE_SONG)
                    ->label(__('filament.resources.singularLabel.song'))
                    ->relationship(ThemeModel::RELATION_SONG, Song::ATTRIBUTE_TITLE)
                    ->useScout(Song::class)
                    ->createOptionForm(SongResource::form($form)->getComponents()),
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
                TextColumn::make(ThemeModel::RELATION_ANIME.'.'.AnimeModel::ATTRIBUTE_NAME)
                    ->label(__('filament.resources.singularLabel.anime'))
                    ->toggleable()
                    ->urlToRelated(AnimeResource::class, ThemeModel::RELATION_ANIME, limit: 30)
                    ->tooltip(fn (TextColumn $column) => $column->getState()),

                TextColumn::make(ThemeModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id'))
                    ->sortable(),

                TextColumn::make(ThemeModel::ATTRIBUTE_TYPE)
                    ->label(__('filament.fields.anime_theme.type.name'))
                    ->toggleable()
                    ->formatStateUsing(fn ($state) => $state->localize()),

                TextColumn::make(ThemeModel::ATTRIBUTE_SEQUENCE)
                    ->label(__('filament.fields.anime_theme.sequence.name'))
                    ->sortable()
                    ->toggleable()
                    ->placeholder('-'),

                TextColumn::make(ThemeModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.anime_theme.slug.name'))
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(fn ($state) => ThemeModel::find(intval($state))->getName()),

                TextColumn::make(ThemeModel::RELATION_GROUP.'.'.Group::ATTRIBUTE_NAME)
                    ->label(__('filament.resources.singularLabel.group'))
                    ->toggleable()
                    ->placeholder('-')
                    ->urlToRelated(GroupResource::class, ThemeModel::RELATION_GROUP),

                TextColumn::make(ThemeModel::RELATION_SONG.'.'.Song::ATTRIBUTE_TITLE)
                    ->label(__('filament.resources.singularLabel.song'))
                    ->toggleable()
                    ->placeholder('-')
                    ->urlToRelated(SongResource::class, ThemeModel::RELATION_SONG, limit: 30)
                    ->tooltip(fn (TextColumn $column) => $column->getState()),
            ])
            ->searchable()
            ->defaultSort(ThemeModel::ATTRIBUTE_ID, 'desc')
            ->filters(static::getFilters())
            ->filtersFormMaxHeight('400px')
            ->actions(static::getActions())
            ->bulkActions(static::getBulkActions());
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

                        TextEntry::make(ThemeModel::RELATION_ANIME.'.'.AnimeModel::ATTRIBUTE_NAME)
                            ->label(__('filament.resources.singularLabel.anime'))
                            ->urlToRelated(AnimeResource::class, ThemeModel::RELATION_ANIME),

                        TextEntry::make(ThemeModel::ATTRIBUTE_TYPE)
                            ->label(__('filament.fields.anime_theme.type.name'))
                            ->formatStateUsing(fn ($state) => $state->localize()),

                        TextEntry::make(ThemeModel::ATTRIBUTE_SEQUENCE)
                            ->label(__('filament.fields.anime_theme.sequence.name'))
                            ->placeholder('-'),

                        TextEntry::make(ThemeModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.anime_theme.slug.name'))
                            ->formatStateUsing(fn ($state) => ThemeModel::find(intval($state))->getName()),

                        TextEntry::make(ThemeModel::RELATION_GROUP.'.'.Group::ATTRIBUTE_NAME)
                            ->label(__('filament.resources.singularLabel.group'))
                            ->placeholder('-')
                            ->urlToRelated(GroupResource::class, ThemeModel::RELATION_GROUP),

                        TextEntry::make(ThemeModel::RELATION_SONG.'.'.Song::ATTRIBUTE_TITLE)
                            ->label(__('filament.resources.singularLabel.song'))
                            ->placeholder('-')
                            ->urlToRelated(SongResource::class, ThemeModel::RELATION_SONG),
                    ])
                    ->columns(3),

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
            $slug = $slug->append(strval(empty($group) ? '' : '-'.Group::find(intval($group))->slug));
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
            RelationGroup::make(static::getLabel(), [
                EntryThemeRelationManager::class,
            ]),
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
                SelectFilter::make(ThemeModel::ATTRIBUTE_TYPE)
                    ->label(__('filament.fields.anime_theme.type.name'))
                    ->options(ThemeType::asSelectArray()),

                NumberFilter::make(ThemeModel::ATTRIBUTE_SEQUENCE)
                    ->labels(__('filament.filters.anime_theme.sequence_from'), __('filament.filters.anime_theme.sequence_to'))
                    ->attribute(ThemeModel::ATTRIBUTE_SEQUENCE),
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
            'index' => ListThemes::route('/'),
            'create' => CreateTheme::route('/create'),
            'view' => ViewTheme::route('/{record:theme_id}'),
            'edit' => EditTheme::route('/{record:theme_id}/edit'),
        ];
    }
}
