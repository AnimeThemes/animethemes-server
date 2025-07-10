<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Actions\Models\Wiki\Anime\AttachAnimeResourceAction;
use App\Filament\Actions\Models\Wiki\Anime\BackfillAnimeAction;
use App\Filament\Actions\Models\Wiki\Anime\DiscordThreadAction;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Fields\Slug;
use App\Filament\Components\Filters\NumberFilter;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Anime\Pages\ListAnimes;
use App\Filament\Resources\Wiki\Anime\Pages\ViewAnime;
use App\Filament\Resources\Wiki\Anime\RelationManagers\ImageAnimeRelationManager;
use App\Filament\Resources\Wiki\Anime\RelationManagers\ResourceAnimeRelationManager;
use App\Filament\Resources\Wiki\Anime\RelationManagers\SeriesAnimeRelationManager;
use App\Filament\Resources\Wiki\Anime\RelationManagers\StudioAnimeRelationManager;
use App\Filament\Resources\Wiki\Anime\RelationManagers\SynonymAnimeRelationManager;
use App\Filament\Resources\Wiki\Anime\RelationManagers\ThemeAnimeRelationManager;
use App\Models\Wiki\Anime as AnimeModel;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Anime.
 */
class Anime extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = AnimeModel::class;

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.anime');
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
        return __('filament.resources.label.anime');
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
        return __('filament-icons.resources.anime');
    }

    /**
     * Get the title attribute for the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRecordTitleAttribute(): string
    {
        return AnimeModel::ATTRIBUTE_NAME;
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
     */
    public static function getRecordSlug(): string
    {
        return 'anime';
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
                TextInput::make(AnimeModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.anime.name.name'))
                    ->helperText(__('filament.fields.anime.name.help'))
                    ->required()
                    ->maxLength(255)
                    ->afterStateUpdatedJs(<<<'JS'
                        $set('slug', slug($state ?? ''));
                    JS),

                Slug::make(AnimeModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.anime.slug.name'))
                    ->helperText(__('filament.fields.anime.slug.help')),

                TextInput::make(AnimeModel::ATTRIBUTE_YEAR)
                    ->label(__('filament.fields.anime.year.name'))
                    ->helperText(__('filament.fields.anime.year.help'))
                    ->required()
                    ->integer()
                    ->length(4)
                    ->default(date('Y'))
                    ->minValue(1960)
                    ->maxValue(intval(date('Y')) + 1),

                Select::make(AnimeModel::ATTRIBUTE_SEASON)
                    ->label(__('filament.fields.anime.season.name'))
                    ->helperText(__('filament.fields.anime.season.help'))
                    ->options(AnimeSeason::asSelectArrayStyled())
                    ->required()
                    ->enum(AnimeSeason::class)
                    ->default(AnimeSeason::getCurrentSeason())
                    ->searchable()
                    ->allowHtml(),

                Select::make(AnimeModel::ATTRIBUTE_MEDIA_FORMAT)
                    ->label(__('filament.fields.anime.media_format.name'))
                    ->helperText(__('filament.fields.anime.media_format.help'))
                    ->options(AnimeMediaFormat::class)
                    ->required(),

                MarkdownEditor::make(AnimeModel::ATTRIBUTE_SYNOPSIS)
                    ->label(__('filament.fields.anime.synopsis.name'))
                    ->helperText(__('filament.fields.anime.synopsis.help'))
                    ->columnSpan(2)
                    ->maxLength(65535),
            ])
            ->columns(2);
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
                TextColumn::make(AnimeModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(AnimeModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.anime.name.name'))
                    ->copyableWithMessage()
                    ->limit(50)
                    ->tooltip(fn (TextColumn $column) => $column->getState()),

                TextColumn::make(AnimeModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.anime.slug.name'))
                    ->limit(20)
                    ->tooltip(fn (TextColumn $column) => $column->getState()),

                TextColumn::make(AnimeModel::ATTRIBUTE_YEAR)
                    ->label(__('filament.fields.anime.year.name')),

                TextColumn::make(AnimeModel::ATTRIBUTE_SEASON)
                    ->label(__('filament.fields.anime.season.name'))
                    ->formatStateUsing(fn (AnimeSeason $state) => $state->localizeStyled())
                    ->html(),

                TextColumn::make(AnimeModel::ATTRIBUTE_MEDIA_FORMAT)
                    ->label(__('filament.fields.anime.media_format.name'))
                    ->formatStateUsing(fn (AnimeMediaFormat $state) => $state->localize()),
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
                        TextEntry::make(AnimeModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(AnimeModel::ATTRIBUTE_NAME)
                            ->label(__('filament.fields.anime.name.name'))
                            ->copyableWithMessage(),

                        TextEntry::make(AnimeModel::ATTRIBUTE_SLUG)
                            ->label(__('filament.fields.anime.slug.name'))
                            ->limit(60),

                        TextEntry::make(AnimeModel::ATTRIBUTE_YEAR)
                            ->label(__('filament.fields.anime.year.name')),

                        TextEntry::make(AnimeModel::ATTRIBUTE_SEASON)
                            ->label(__('filament.fields.anime.season.name'))
                            ->formatStateUsing(fn (AnimeSeason $state) => $state->localizeStyled())
                            ->html(),

                        TextEntry::make(AnimeModel::ATTRIBUTE_MEDIA_FORMAT)
                            ->label(__('filament.fields.anime.media_format.name'))
                            ->formatStateUsing(fn (AnimeMediaFormat $state) => $state->localize()),

                        TextEntry::make(AnimeModel::ATTRIBUTE_SYNOPSIS)
                            ->label(__('filament.fields.anime.synopsis.name'))
                            ->markdown()
                            ->columnSpanFull(),
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
            RelationGroup::make(static::getModelLabel(), [
                SynonymAnimeRelationManager::class,
                ThemeAnimeRelationManager::class,
                SeriesAnimeRelationManager::class,
                ResourceAnimeRelationManager::class,
                ImageAnimeRelationManager::class,
                StudioAnimeRelationManager::class,

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
            NumberFilter::make(AnimeModel::ATTRIBUTE_YEAR)
                ->label(__('filament.fields.anime.year.name')),

            SelectFilter::make(AnimeModel::ATTRIBUTE_SEASON)
                ->label(__('filament.fields.anime.season.name'))
                ->options(AnimeSeason::class),

            SelectFilter::make(AnimeModel::ATTRIBUTE_MEDIA_FORMAT)
                ->label(__('filament.fields.anime.media_format.name'))
                ->options(AnimeMediaFormat::class),

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
        $streamingResourceSites = [
            ResourceSite::CRUNCHYROLL,
            ResourceSite::HIDIVE,
            ResourceSite::NETFLIX,
            ResourceSite::DISNEY_PLUS,
            ResourceSite::HULU,
            ResourceSite::AMAZON_PRIME_VIDEO,
        ];

        return [
            DiscordThreadAction::make(),

            BackfillAnimeAction::make(),

            AttachAnimeResourceAction::make(),

            AttachAnimeResourceAction::make('attach-anime-streaming-resource')
                ->label(__('filament.actions.models.wiki.attach_streaming_resource.name'))
                ->icon(__('filament-icons.actions.anime.attach_streaming_resource'))
                ->sites($streamingResourceSites),
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
            'index' => ListAnimes::route('/'),
            'view' => ViewAnime::route('/{record:anime_id}'),
        ];
    }
}
