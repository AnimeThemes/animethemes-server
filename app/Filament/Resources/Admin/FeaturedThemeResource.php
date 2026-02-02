<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin;

use App\Enums\Filament\NavigationGroup;
use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\BelongsTo;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Infolist\BelongsToEntry;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\Admin\FeaturedTheme\Pages\ListFeaturedThemes;
use App\Filament\Resources\Admin\FeaturedTheme\Pages\ViewFeaturedTheme;
use App\Filament\Resources\Auth\UserResource;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Anime\Theme\EntryResource;
use App\Filament\Resources\Wiki\VideoResource;
use App\Models\Admin\FeaturedTheme;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class FeaturedThemeResource extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = FeaturedTheme::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.featured_theme');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.featured_themes');
    }

    public static function getNavigationGroup(): NavigationGroup
    {
        return NavigationGroup::ADMIN;
    }

    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::OutlinedCalendarDays;
    }

    public static function getRecordSlug(): string
    {
        return 'featured-themes';
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Necessary to prevent lazy loading when loading related resources
        return $query->with([
            FeaturedTheme::RELATION_USER,
            FeaturedTheme::RELATION_VIDEO,
            'animethemeentry.anime',
            'animethemeentry.animetheme.group',
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make(FeaturedTheme::ATTRIBUTE_START_AT)
                    ->label(__('filament.fields.featured_theme.start_at.name'))
                    ->helperText(__('filament.fields.featured_theme.start_at.help'))
                    ->native(false)
                    ->required()
                    ->before(FeaturedTheme::ATTRIBUTE_END_AT),

                DatePicker::make(FeaturedTheme::ATTRIBUTE_END_AT)
                    ->label(__('filament.fields.featured_theme.end_at.name'))
                    ->helperText(__('filament.fields.featured_theme.end_at.help'))
                    ->native(false)
                    ->required()
                    ->after(FeaturedTheme::ATTRIBUTE_START_AT),

                BelongsTo::make(FeaturedTheme::ATTRIBUTE_ENTRY)
                    ->resource(EntryResource::class)
                    ->live(true)
                    ->required()
                    ->rules([
                        fn (Get $get): Closure => (fn (): array => [
                            Rule::when(
                                filled($get(FeaturedTheme::RELATION_ENTRY)) && filled($get(FeaturedTheme::RELATION_VIDEO)),
                                [
                                    Rule::exists(AnimeThemeEntryVideo::class, AnimeThemeEntryVideo::ATTRIBUTE_ENTRY)
                                        ->where(AnimeThemeEntryVideo::ATTRIBUTE_VIDEO, $get(FeaturedTheme::RELATION_VIDEO)),
                                ]
                            )]),
                    ]),

                Select::make(FeaturedTheme::ATTRIBUTE_VIDEO)
                    ->label(__('filament.resources.singularLabel.video'))
                    ->relationship(FeaturedTheme::RELATION_VIDEO, Video::ATTRIBUTE_FILENAME)
                    ->required()
                    ->rules([
                        fn (Get $get): Closure => (fn (): array => [
                            Rule::when(
                                filled($get(FeaturedTheme::RELATION_ENTRY)) && filled($get(FeaturedTheme::RELATION_VIDEO)),
                                [
                                    Rule::exists(AnimeThemeEntryVideo::class, AnimeThemeEntryVideo::ATTRIBUTE_VIDEO)
                                        ->where(AnimeThemeEntryVideo::ATTRIBUTE_ENTRY, $get(FeaturedTheme::RELATION_ENTRY)),
                                ]
                            )]),
                    ])
                    ->options(fn (Get $get) => Video::query()
                        ->whereRelation(Video::RELATION_ANIMETHEMEENTRIES, function ($query) use ($get): void {
                            $query->whereKey($get(FeaturedTheme::ATTRIBUTE_ENTRY));
                        })
                        ->get()
                        ->mapWithKeys(fn (Video $video): array => [$video->getKey() => $video->getName()])
                        ->toArray()),

                BelongsTo::make(FeaturedTheme::ATTRIBUTE_USER)
                    ->resource(UserResource::class)
                    ->default(Auth::id()),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make(FeaturedTheme::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(FeaturedTheme::ATTRIBUTE_START_AT)
                    ->label(__('filament.fields.featured_theme.start_at.name'))
                    ->date(),

                TextColumn::make(FeaturedTheme::ATTRIBUTE_END_AT)
                    ->label(__('filament.fields.featured_theme.end_at.name'))
                    ->date(),

                BelongsToColumn::make(FeaturedTheme::RELATION_VIDEO, VideoResource::class),

                BelongsToColumn::make(FeaturedTheme::RELATION_ENTRY, EntryResource::class),

                BelongsToColumn::make(FeaturedTheme::RELATION_USER, UserResource::class),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
                    ->schema([
                        TextEntry::make(FeaturedTheme::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(FeaturedTheme::ATTRIBUTE_START_AT)
                            ->label(__('filament.fields.featured_theme.start_at.name'))
                            ->date(),

                        TextEntry::make(FeaturedTheme::ATTRIBUTE_END_AT)
                            ->label(__('filament.fields.featured_theme.end_at.name'))
                            ->date(),

                        BelongsToEntry::make(FeaturedTheme::RELATION_VIDEO, VideoResource::class),

                        BelongsToEntry::make(FeaturedTheme::RELATION_ENTRY, EntryResource::class),

                        BelongsToEntry::make(FeaturedTheme::RELATION_USER, UserResource::class),
                    ])
                    ->columns(3),

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
                ...parent::getBaseRelations(),
            ]),
        ];
    }

    /**
     * @return array<string, \Filament\Resources\Pages\PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListFeaturedThemes::route('/'),
            'view' => ViewFeaturedTheme::route('/{record:featured_theme_id}'),
        ];
    }
}
