<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime;

use App\Enums\Models\Wiki\AnimeSynonymType;
use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\BelongsTo;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Fields\TextInput;
use App\Filament\Components\Infolist\BelongsToEntry;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\RelationManagers\Wiki\Anime\SynonymRelationManager;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Anime as AnimeResource;
use App\Filament\Resources\Wiki\Anime\RelationManagers\SynonymAnimeRelationManager;
use App\Filament\Resources\Wiki\Anime\Synonym\Pages\ListSynonyms;
use App\Filament\Resources\Wiki\Anime\Synonym\Pages\ViewSynonym;
use App\Models\Wiki\Anime\AnimeSynonym;
use App\Models\Wiki\Anime\AnimeSynonym as SynonymModel;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Synonym extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = SynonymModel::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.anime_synonym');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.anime_synonyms');
    }

    public static function getNavigationGroup(): string
    {
        return __('filament.resources.group.wiki');
    }

    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::OutlinedGlobeAlt;
    }

    public static function getRecordSlug(): string
    {
        return 'anime-synonyms';
    }

    public static function getRecordTitleAttribute(): string
    {
        return SynonymModel::ATTRIBUTE_TEXT;
    }

    /**
     * @return Builder
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Necessary to prevent lazy loading when loading related resources
        return $query->with([AnimeSynonym::RELATION_ANIME]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                BelongsTo::make(SynonymModel::ATTRIBUTE_ANIME)
                    ->resource(AnimeResource::class)
                    ->hiddenOn(SynonymRelationManager::class),

                Select::make(SynonymModel::ATTRIBUTE_TYPE)
                    ->label(__('filament.fields.anime_synonym.type.name'))
                    ->helperText(__('filament.fields.anime_synonym.type.help'))
                    ->options(AnimeSynonymType::class)
                    ->required(),

                TextInput::make(SynonymModel::ATTRIBUTE_TEXT)
                    ->label(__('filament.fields.anime_synonym.text.name'))
                    ->helperText(__('filament.fields.anime_synonym.text.help'))
                    ->required()
                    ->maxLength(255),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make(SynonymModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                BelongsToColumn::make(SynonymModel::RELATION_ANIME, AnimeResource::class)
                    ->hiddenOn(SynonymAnimeRelationManager::class),

                TextColumn::make(SynonymModel::ATTRIBUTE_TYPE)
                    ->label(__('filament.fields.anime_synonym.type.name'))
                    ->formatStateUsing(fn (AnimeSynonymType $state) => $state->localize()),

                TextColumn::make(SynonymModel::ATTRIBUTE_TEXT)
                    ->label(__('filament.fields.anime_synonym.text.name'))
                    ->limit(50)
                    ->tooltip(fn (TextColumn $column) => $column->getState()),
            ])
            ->searchable();
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
                    ->schema([
                        TextEntry::make(SynonymModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        BelongsToEntry::make(SynonymModel::RELATION_ANIME, AnimeResource::class),

                        TextEntry::make(SynonymModel::ATTRIBUTE_TYPE)
                            ->label(__('filament.fields.anime_synonym.type.name'))
                            ->formatStateUsing(fn (AnimeSynonymType $state) => $state->localize()),

                        TextEntry::make(SynonymModel::ATTRIBUTE_TEXT)
                            ->label(__('filament.fields.anime_synonym.text.name'))
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                TimestampSection::make(),
            ])
            ->columns(2);
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
     * @return \Filament\Tables\Filters\BaseFilter[]
     */
    public static function getFilters(): array
    {
        return [
            SelectFilter::make(SynonymModel::ATTRIBUTE_TYPE)
                ->label(__('filament.fields.anime_synonym.type.name'))
                ->options(AnimeSynonymType::class),

            ...parent::getFilters(),
        ];
    }

    /**
     * @return array<string, \Filament\Resources\Pages\PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListSynonyms::route('/'),
            'view' => ViewSynonym::route('/{record:synonym_id}'),
        ];
    }
}
