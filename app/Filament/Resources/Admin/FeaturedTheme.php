<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Section;
use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\BelongsTo;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Infolist\BelongsToEntry;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\Admin\FeaturedTheme\Pages\ListFeaturedThemes;
use App\Filament\Resources\Admin\FeaturedTheme\Pages\ViewFeaturedTheme;
use App\Filament\Resources\Auth\User as UserResource;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Anime\Theme\Entry as EntryResource;
use App\Filament\Resources\Wiki\Video as VideoResource;
use App\Models\Admin\FeaturedTheme as FeaturedThemeModel;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Class FeaturedTheme.
 */
class FeaturedTheme extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
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
        return __('filament-icons.resources.featured_themes');
    }

    /**
     * Get the slug (URI key) for the resource.
     *
     * @return string
     */
    public static function getRecordSlug(): string
    {
        return 'featured-themes';
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
        return $query->with([
            FeaturedThemeModel::RELATION_USER,
            FeaturedThemeModel::RELATION_VIDEO,
            'animethemeentry.anime',
            'animethemeentry.animetheme.group',
        ]);
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
                DatePicker::make(FeaturedThemeModel::ATTRIBUTE_START_AT)
                    ->label(__('filament.fields.featured_theme.start_at.name'))
                    ->helperText(__('filament.fields.featured_theme.start_at.help'))
                    ->native(false)
                    ->required()
                    ->before(FeaturedThemeModel::ATTRIBUTE_END_AT),

                DatePicker::make(FeaturedThemeModel::ATTRIBUTE_END_AT)
                    ->label(__('filament.fields.featured_theme.end_at.name'))
                    ->helperText(__('filament.fields.featured_theme.end_at.help'))
                    ->native(false)
                    ->required()
                    ->after(FeaturedThemeModel::ATTRIBUTE_START_AT),

                BelongsTo::make(FeaturedThemeModel::ATTRIBUTE_ENTRY)
                    ->resource(EntryResource::class)
                    ->live(true)
                    ->required()
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
                    ->required()
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
                            ->whereRelation(Video::RELATION_ANIMETHEMEENTRIES, function ($query) use ($get) {
                                $query->whereKey($get(FeaturedThemeModel::ATTRIBUTE_ENTRY));
                            })
                            ->get()
                            ->mapWithKeys(fn (Video $video) => [$video->getKey() => $video->getName()])
                            ->toArray();
                    }),

                BelongsTo::make(FeaturedThemeModel::ATTRIBUTE_USER)
                    ->resource(UserResource::class)
                    ->default(Auth::id()),
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
                TextColumn::make(FeaturedThemeModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(FeaturedThemeModel::ATTRIBUTE_START_AT)
                    ->label(__('filament.fields.featured_theme.start_at.name'))
                    ->date(),

                TextColumn::make(FeaturedThemeModel::ATTRIBUTE_END_AT)
                    ->label(__('filament.fields.featured_theme.end_at.name'))
                    ->date(),

                BelongsToColumn::make(FeaturedThemeModel::RELATION_VIDEO, VideoResource::class),

                BelongsToColumn::make(FeaturedThemeModel::RELATION_ENTRY, EntryResource::class),

                BelongsToColumn::make(FeaturedThemeModel::RELATION_USER, UserResource::class),
            ]);
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
                        TextEntry::make(FeaturedThemeModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(FeaturedThemeModel::ATTRIBUTE_START_AT)
                            ->label(__('filament.fields.featured_theme.start_at.name'))
                            ->date(),

                        TextEntry::make(FeaturedThemeModel::ATTRIBUTE_END_AT)
                            ->label(__('filament.fields.featured_theme.end_at.name'))
                            ->date(),

                        BelongsToEntry::make(FeaturedThemeModel::RELATION_VIDEO, VideoResource::class),

                        BelongsToEntry::make(FeaturedThemeModel::RELATION_ENTRY, EntryResource::class),

                        BelongsToEntry::make(FeaturedThemeModel::RELATION_USER, UserResource::class),
                    ])
                    ->columns(3),

                TimestampSection::make(),
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
            RelationGroup::make(static::getLabel(), [
                ...parent::getBaseRelations(),
            ]),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     */
    public static function getFilters(): array
    {
        return [
            ...parent::getFilters(),
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array
     */
    public static function getRecordActions(): array
    {
        return [
            ...parent::getRecordActions(),
        ];
    }

    /**
     * Get the bulk actions available for the resource.
     *
     * @param  array|null  $actionsIncludedInGroup
     * @return array
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return [
            ...parent::getBulkActions(),
        ];
    }

    /**
     * Get the table actions available for the resource.
     *
     * @return array
     */
    public static function getTableActions(): array
    {
        return [
            ...parent::getTableActions(),
        ];
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
            'view' => ViewFeaturedTheme::route('/{record:featured_theme_id}'),
        ];
    }
}
