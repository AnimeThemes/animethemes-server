<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Enums\Filament\NavigationGroup;
use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Actions\Models\Wiki\Anime\AttachAnimeResourceAction;
use App\Filament\Actions\Models\Wiki\Anime\BackfillAnimeAction;
use App\Filament\Actions\Models\Wiki\Anime\DiscordThreadAction;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Fields\Slug;
use App\Filament\Components\Fields\TextInput;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\RelationManagers\Wiki\ImageRelationManager;
use App\Filament\RelationManagers\Wiki\ResourceRelationManager;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Anime\Pages\ListAnimes;
use App\Filament\Resources\Wiki\Anime\Pages\ViewAnime;
use App\Filament\Resources\Wiki\Anime\RelationManagers\SeriesAnimeRelationManager;
use App\Filament\Resources\Wiki\Anime\RelationManagers\StudioAnimeRelationManager;
use App\Filament\Resources\Wiki\Anime\RelationManagers\SynonymAnimeRelationManager;
use App\Filament\Resources\Wiki\Anime\RelationManagers\ThemeAnimeRelationManager;
use App\Models\Wiki\Anime;
use Filament\Forms\Components\MarkdownEditor;
use Filament\QueryBuilder\Constraints\NumberConstraint;
use Filament\QueryBuilder\Constraints\SelectConstraint;
use Filament\QueryBuilder\Constraints\TextConstraint;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AnimeResource extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = Anime::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.anime');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.anime');
    }

    public static function getNavigationGroup(): NavigationGroup
    {
        return NavigationGroup::CONTENT;
    }

    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::OutlinedTv;
    }

    public static function getRecordTitleAttribute(): string
    {
        return Anime::ATTRIBUTE_NAME;
    }

    public static function canGloballySearch(): bool
    {
        return true;
    }

    public static function getRecordSlug(): string
    {
        return 'anime';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make(Anime::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.anime.name.name'))
                    ->helperText(__('filament.fields.anime.name.help'))
                    ->required()
                    ->maxLength(255)
                    ->afterStateUpdatedJs(<<<'JS'
                        $set('slug', slug($state ?? ''));
                    JS),

                Slug::make(Anime::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.anime.slug.name'))
                    ->helperText(__('filament.fields.anime.slug.help')),

                TextInput::make(Anime::ATTRIBUTE_YEAR)
                    ->label(__('filament.fields.anime.year.name'))
                    ->helperText(__('filament.fields.anime.year.help'))
                    ->required()
                    ->integer()
                    ->length(4)
                    ->default(date('Y'))
                    ->minValue(1960)
                    ->maxValue(intval(date('Y')) + 1),

                Select::make(Anime::ATTRIBUTE_SEASON)
                    ->label(__('filament.fields.anime.season.name'))
                    ->helperText(__('filament.fields.anime.season.help'))
                    ->options(AnimeSeason::asSelectArrayStyled())
                    ->required()
                    ->enum(AnimeSeason::class)
                    ->default(AnimeSeason::getCurrentSeason())
                    ->searchable()
                    ->allowHtml(),

                Select::make(Anime::ATTRIBUTE_MEDIA_FORMAT)
                    ->label(__('filament.fields.anime.media_format.name'))
                    ->helperText(__('filament.fields.anime.media_format.help'))
                    ->options(AnimeMediaFormat::class)
                    ->required(),

                MarkdownEditor::make(Anime::ATTRIBUTE_SYNOPSIS)
                    ->label(__('filament.fields.anime.synopsis.name'))
                    ->helperText(__('filament.fields.anime.synopsis.help'))
                    ->columnSpan(2)
                    ->maxLength(65535),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make(Anime::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(Anime::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.anime.name.name'))
                    ->copyableWithMessage()
                    ->limit(50)
                    ->tooltip(fn (TextColumn $column): mixed => $column->getState()),

                TextColumn::make(Anime::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.anime.slug.name'))
                    ->limit(20)
                    ->tooltip(fn (TextColumn $column): mixed => $column->getState()),

                TextColumn::make(Anime::ATTRIBUTE_YEAR)
                    ->label(__('filament.fields.anime.year.name')),

                TextColumn::make(Anime::ATTRIBUTE_SEASON)
                    ->label(__('filament.fields.anime.season.name'))
                    ->formatStateUsing(fn (AnimeSeason $state): string => $state->localizeStyled())
                    ->html(),

                TextColumn::make(Anime::ATTRIBUTE_MEDIA_FORMAT)
                    ->label(__('filament.fields.anime.media_format.name'))
                    ->formatStateUsing(fn (AnimeMediaFormat $state): ?string => $state->localize()),
            ])
            ->searchable();
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
                    ->schema([
                        TextEntry::make(Anime::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(Anime::ATTRIBUTE_NAME)
                            ->label(__('filament.fields.anime.name.name'))
                            ->copyableWithMessage(),

                        TextEntry::make(Anime::ATTRIBUTE_SLUG)
                            ->label(__('filament.fields.anime.slug.name'))
                            ->limit(60),

                        TextEntry::make(Anime::ATTRIBUTE_YEAR)
                            ->label(__('filament.fields.anime.year.name')),

                        TextEntry::make(Anime::ATTRIBUTE_SEASON)
                            ->label(__('filament.fields.anime.season.name'))
                            ->formatStateUsing(fn (AnimeSeason $state): string => $state->localizeStyled())
                            ->html(),

                        TextEntry::make(Anime::ATTRIBUTE_MEDIA_FORMAT)
                            ->label(__('filament.fields.anime.media_format.name'))
                            ->formatStateUsing(fn (AnimeMediaFormat $state): ?string => $state->localize()),

                        TextEntry::make(Anime::ATTRIBUTE_SYNOPSIS)
                            ->label(__('filament.fields.anime.synopsis.name'))
                            ->markdown()
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

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
                    TextConstraint::make(Anime::ATTRIBUTE_NAME)
                        ->label(__('filament.fields.anime.name.name')),

                    TextConstraint::make(Anime::ATTRIBUTE_SLUG)
                        ->label(__('filament.fields.anime.slug.name')),

                    NumberConstraint::make(Anime::ATTRIBUTE_YEAR)
                        ->label(__('filament.fields.anime.year.name')),

                    SelectConstraint::make(Anime::ATTRIBUTE_SEASON)
                        ->label(__('filament.fields.anime.season.name'))
                        ->options(AnimeSeason::class)
                        ->multiple(),

                    SelectConstraint::make(Anime::ATTRIBUTE_MEDIA_FORMAT)
                        ->label(__('filament.fields.anime.media_format.name'))
                        ->options(AnimeMediaFormat::class)
                        ->multiple(),

                    TextConstraint::make(Anime::ATTRIBUTE_SYNOPSIS)
                        ->label(__('filament.fields.anime.synopsis.name')),

                    ...parent::getConstraints(),
                ]),

            ...parent::getFilters(),
        ];
    }

    /**
     * @return array<int, \Filament\Actions\Action|\Filament\Actions\ActionGroup>
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
                ->icon(Heroicon::OutlinedTv)
                ->sites($streamingResourceSites),
        ];
    }

    /**
     * @return array<int, RelationGroup|class-string<\Filament\Resources\RelationManagers\RelationManager>>
     */
    public static function getRelations(): array
    {
        return [
            RelationGroup::make(static::getModelLabel(), [
                SynonymAnimeRelationManager::class,
                ThemeAnimeRelationManager::class,
                SeriesAnimeRelationManager::class,
                ResourceRelationManager::class,
                ImageRelationManager::class,
                StudioAnimeRelationManager::class,

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
            'index' => ListAnimes::route('/'),
            'view' => ViewAnime::route('/{record:anime_id}'),
        ];
    }
}
