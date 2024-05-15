<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin;

use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Admin\FeaturedTheme\Pages\CreateFeaturedTheme;
use App\Filament\Resources\Admin\FeaturedTheme\Pages\EditFeaturedTheme;
use App\Filament\Resources\Admin\FeaturedTheme\Pages\ListFeaturedThemes;
use App\Filament\Resources\Admin\FeaturedTheme\Pages\ViewFeaturedTheme;
use App\Filament\Resources\Auth\User as UserResource;
use App\Filament\Resources\Wiki\Anime\Theme\Entry as EntryResource;
use App\Filament\Resources\Wiki\Video as VideoResource;
use App\Models\Admin\FeaturedTheme as FeaturedThemeModel;
use App\Models\Auth\User;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry as EntryModel;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Class FeaturedTheme.
 */
class FeaturedTheme extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string|null
     */
    protected static ?string $model = FeaturedThemeModel::class;

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getLabel(): string
    {
        return __('filament.resources.singularLabel.featured_theme');
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
        return __('filament.resources.label.featured_themes');
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
        return __('filament.resources.group.admin');
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
        return __('filament.resources.icon.featured_themes');
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
        return static::getDefaultSlug().'featured-themes';
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
        return FeaturedThemeModel::ATTRIBUTE_ID;
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
        $allowedDateFormats = array_column(AllowedDateFormat::cases(), 'value');

        return $form
            ->schema([
                DatePicker::make(FeaturedThemeModel::ATTRIBUTE_START_AT)
                    ->label(__('filament.fields.featured_theme.start_at.name'))
                    ->helperText(__('filament.fields.featured_theme.start_at.help'))
                    ->required()
                    ->rules([
                        'required',
                        Str::of('date_format:')
                            ->append(implode(',', $allowedDateFormats))
                            ->__toString(),
                        Str::of('before:')
                            ->append(FeaturedThemeModel::ATTRIBUTE_END_AT)
                            ->__toString(),
                    ]),

                DatePicker::make(FeaturedThemeModel::ATTRIBUTE_END_AT)
                    ->label(__('filament.fields.featured_theme.end_at.name'))
                    ->helperText(__('filament.fields.featured_theme.end_at.help'))
                    ->required()
                    ->rules([
                        'required',
                        Str::of('date_format:')
                            ->append(implode(',', $allowedDateFormats))
                            ->__toString(),
                        Str::of('after:')
                            ->append(FeaturedThemeModel::ATTRIBUTE_START_AT)
                            ->__toString(),
                    ]),

                Select::make(FeaturedThemeModel::ATTRIBUTE_ENTRY)
                    ->label(__('filament.resources.singularLabel.anime_theme_entry'))
                    ->relationship(FeaturedThemeModel::RELATION_ENTRY, EntryModel::ATTRIBUTE_ID)
                    ->live(true)
                    ->useScout(EntryModel::class, EntryModel::RELATION_ANIME_SHALLOW)
                    ->rules([
                        fn (Get $get) => function () use ($get) {
                            return [
                                Rule::when(
                                    ! empty($get(FeaturedThemeModel::RELATION_ENTRY)) && ! empty($get(FeaturedThemeModel::RELATION_VIDEO)),
                                    [
                                        Rule::exists(AnimeThemeEntryVideo::class, AnimeThemeEntryVideo::ATTRIBUTE_ENTRY)
                                            ->where(AnimeThemeEntryVideo::ATTRIBUTE_VIDEO, $get(FeaturedThemeModel::RELATION_VIDEO)),
                                    ]
                            )];
                        }
                    ]),

                Select::make(FeaturedThemeModel::ATTRIBUTE_VIDEO)
                    ->label(__('filament.resources.singularLabel.video'))
                    ->relationship(FeaturedThemeModel::RELATION_VIDEO, Video::ATTRIBUTE_FILENAME)
                    ->rules([
                        fn (Get $get) => function () use ($get) {
                            return [
                                Rule::when(
                                    ! empty($get(FeaturedThemeModel::RELATION_ENTRY)) && ! empty($get(FeaturedThemeModel::RELATION_VIDEO)),
                                    [
                                        Rule::exists(AnimeThemeEntryVideo::class, AnimeThemeEntryVideo::ATTRIBUTE_VIDEO)
                                            ->where(AnimeThemeEntryVideo::ATTRIBUTE_ENTRY, $get(FeaturedThemeModel::RELATION_ENTRY)),
                                    ]
                            )];
                        }
                    ])
                    ->options(function (Get $get) {
                        return Video::query()
                            ->whereHas(Video::RELATION_ANIMETHEMEENTRIES, function ($query) use ($get) {
                                $query->where(EntryModel::TABLE.'.'.EntryModel::ATTRIBUTE_ID, $get(FeaturedThemeModel::ATTRIBUTE_ENTRY));
                            })
                            ->get()
                            ->mapWithKeys(fn (Video $video) => [$video->getKey() => $video->getName()])
                            ->toArray();
                    }),

                Select::make(FeaturedThemeModel::ATTRIBUTE_USER)
                    ->label(__('filament.resources.singularLabel.user'))
                    ->relationship(FeaturedThemeModel::RELATION_USER, User::ATTRIBUTE_NAME)
                    ->searchable(),
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
                TextColumn::make(FeaturedThemeModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id'))
                    ->sortable(),

                TextColumn::make(FeaturedThemeModel::ATTRIBUTE_START_AT)
                    ->label(__('filament.fields.featured_theme.start_at.name'))
                    ->sortable()
                    ->date()
                    ->toggleable(),

                TextColumn::make(FeaturedThemeModel::ATTRIBUTE_END_AT)
                    ->label(__('filament.fields.featured_theme.end_at.name'))
                    ->sortable()
                    ->date()
                    ->toggleable(),

                TextColumn::make(FeaturedThemeModel::RELATION_VIDEO.'.'.Video::ATTRIBUTE_FILENAME)
                    ->label(__('filament.resources.singularLabel.video'))
                    ->toggleable()
                    ->placeholder('-')
                    ->urlToRelated(VideoResource::class, FeaturedThemeModel::RELATION_VIDEO),

                TextColumn::make(FeaturedThemeModel::RELATION_ENTRY.'.'.EntryModel::ATTRIBUTE_ID)
                    ->label(__('filament.resources.singularLabel.anime_theme_entry'))
                    ->toggleable()
                    ->placeholder('-')
                    ->formatStateUsing(fn (string $state) => EntryModel::find(intval($state))->load(EntryModel::RELATION_ANIME_SHALLOW)->getName())
                    ->urlToRelated(EntryResource::class, FeaturedThemeModel::RELATION_ENTRY),

                TextColumn::make(FeaturedThemeModel::RELATION_USER.'.'.User::ATTRIBUTE_NAME)
                    ->label(__('filament.resources.singularLabel.user'))
                    ->toggleable()
                    ->placeholder('-')
                    ->urlToRelated(UserResource::class, FeaturedThemeModel::RELATION_USER),
            ])
            ->defaultSort(FeaturedThemeModel::ATTRIBUTE_ID, 'desc')
            ->filters(static::getFilters())
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
                        TextEntry::make(FeaturedThemeModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(FeaturedThemeModel::ATTRIBUTE_START_AT)
                            ->label(__('filament.fields.featured_theme.start_at.name'))
                            ->date(),

                        TextEntry::make(FeaturedThemeModel::ATTRIBUTE_END_AT)
                            ->label(__('filament.fields.featured_theme.end_at.name'))
                            ->date(),

                        TextEntry::make(FeaturedThemeModel::RELATION_VIDEO.'.'.Video::ATTRIBUTE_FILENAME)
                            ->label(__('filament.resources.singularLabel.video'))
                            ->placeholder('-')
                            ->urlToRelated(VideoResource::class, FeaturedThemeModel::RELATION_VIDEO),

                        TextEntry::make(FeaturedThemeModel::RELATION_ENTRY.'.'.EntryModel::ATTRIBUTE_ID)
                            ->label(__('filament.resources.singularLabel.anime_theme_entry'))
                            ->placeholder('-')
                            ->formatStateUsing(fn (string $state) => EntryModel::find(intval($state))->load(EntryModel::RELATION_ANIME)->getName())
                            ->urlToRelated(EntryResource::class, FeaturedThemeModel::RELATION_ENTRY),

                        TextEntry::make(FeaturedThemeModel::RELATION_USER.'.'.User::ATTRIBUTE_NAME)
                            ->label(__('filament.resources.singularLabel.user'))
                            ->placeholder('-')
                            ->urlToRelated(UserResource::class, FeaturedThemeModel::RELATION_USER),
                    ])
                    ->columns(3),

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
        return [];
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
            'index' => ListFeaturedThemes::route('/'),
            'create' => CreateFeaturedTheme::route('/create'),
            'view' => ViewFeaturedTheme::route('/{record:featured_theme_id}'),
            'edit' => EditFeaturedTheme::route('/{record:featured_theme_id}/edit'),
        ];
    }
}
