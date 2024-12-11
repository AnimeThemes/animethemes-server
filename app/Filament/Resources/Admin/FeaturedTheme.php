<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin;

use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\BelongsTo;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Resources\Admin\FeaturedTheme\Pages\CreateFeaturedTheme;
use App\Filament\Resources\Admin\FeaturedTheme\Pages\EditFeaturedTheme;
use App\Filament\Resources\Admin\FeaturedTheme\Pages\ListFeaturedThemes;
use App\Filament\Resources\Admin\FeaturedTheme\Pages\ViewFeaturedTheme;
use App\Filament\Resources\Auth\User as UserResource;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Anime\Theme\Entry as EntryResource;
use App\Filament\Resources\Wiki\Video as VideoResource;
use App\Models\Admin\FeaturedTheme as FeaturedThemeModel;
use App\Models\Auth\User;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry as EntryModel;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use App\Rules\Admin\StartDateBeforeEndDateRule;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;

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
    public static function getRecordSlug(): string
    {
        return 'featured-themes';
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
                DateRangePicker::make('date')
                    ->label(__('filament.fields.featured_theme.date.name'))
                    ->helperText(__('filament.fields.featured_theme.date.help'))
                    ->timezone('UTC')
                    ->displayFormat('MM/DD/YYYY')
                    ->format(AllowedDateFormat::YMD->value)
                    ->formatStateUsing(fn ($record) => $record !== null ? $record->start_at->format('m/d/Y') . ' - ' . $record->end_at->format('m/d/Y') : null)
                    ->required()
                    ->rules([
                        'required',
                        'string',
                        'regex:/^\d{2}\/\d{2}\/\d{4} - \d{2}\/\d{2}\/\d{4}$/',
                        new StartDateBeforeEndDateRule(),
                    ])
                    ->live(true)
                    ->afterStateUpdated(function (string $state, Set $set) {
                        $dates = explode(' - ', $state);
                        $set(FeaturedThemeModel::ATTRIBUTE_START_AT, Carbon::createFromFormat('m/d/Y', $dates[0]));
                        $set(FeaturedThemeModel::ATTRIBUTE_END_AT, Carbon::createFromFormat('m/d/Y', $dates[1]));
                    }),

                Hidden::make(FeaturedThemeModel::ATTRIBUTE_START_AT),
                Hidden::make(FeaturedThemeModel::ATTRIBUTE_END_AT),

                BelongsTo::make(FeaturedThemeModel::ATTRIBUTE_ENTRY)
                    ->resource(EntryResource::class)
                    ->live(true)
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

                BelongsTo::make(FeaturedThemeModel::ATTRIBUTE_USER)
                    ->resource(UserResource::class),
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
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(FeaturedThemeModel::ATTRIBUTE_START_AT)
                    ->label(__('filament.fields.featured_theme.start_at'))
                    ->date(),

                TextColumn::make(FeaturedThemeModel::ATTRIBUTE_END_AT)
                    ->label(__('filament.fields.featured_theme.end_at'))
                    ->date(),

                BelongsToColumn::make(FeaturedThemeModel::RELATION_VIDEO.'.'.Video::ATTRIBUTE_FILENAME)
                    ->resource(VideoResource::class),

                BelongsToColumn::make(FeaturedThemeModel::RELATION_ENTRY.'.'.EntryModel::ATTRIBUTE_ID)
                    ->resource(EntryResource::class),

                BelongsToColumn::make(FeaturedThemeModel::RELATION_USER.'.'.User::ATTRIBUTE_NAME)
                    ->resource(UserResource::class),
            ]);
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
                            ->label(__('filament.fields.featured_theme.start_at'))
                            ->date(),

                        TextEntry::make(FeaturedThemeModel::ATTRIBUTE_END_AT)
                            ->label(__('filament.fields.featured_theme.end_at'))
                            ->date(),

                        TextEntry::make(FeaturedThemeModel::RELATION_VIDEO.'.'.Video::ATTRIBUTE_FILENAME)
                            ->label(__('filament.resources.singularLabel.video'))
                            ->urlToRelated(VideoResource::class, FeaturedThemeModel::RELATION_VIDEO),

                        TextEntry::make(FeaturedThemeModel::RELATION_ENTRY)
                            ->label(__('filament.resources.singularLabel.anime_theme_entry'))
                            ->urlToRelated(EntryResource::class, FeaturedThemeModel::RELATION_ENTRY, true),

                        TextEntry::make(FeaturedThemeModel::RELATION_USER.'.'.User::ATTRIBUTE_NAME)
                            ->label(__('filament.resources.singularLabel.user'))
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
        return [
            RelationGroup::make(static::getLabel(),
                array_merge(
                    [],
                    parent::getBaseRelations(),
                )
            ),
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
     * @param  array|null  $actionsIncludedInGroup
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
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
     *
     * @noinspection PhpMissingParentCallCommonInspection
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
            'index' => ListFeaturedThemes::route('/'),
            'create' => CreateFeaturedTheme::route('/create'),
            'view' => ViewFeaturedTheme::route('/{record:featured_theme_id}'),
            'edit' => EditFeaturedTheme::route('/{record:featured_theme_id}/edit'),
        ];
    }
}
