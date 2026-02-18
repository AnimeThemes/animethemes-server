<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Enums\Filament\NavigationGroup;
use App\Enums\Models\Wiki\SynonymType;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Fields\TextInput;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Anime\RelationManagers\SynonymAnimeRelationManager;
use App\Filament\Resources\Wiki\Synonym\Pages\ListSynonyms;
use App\Filament\Resources\Wiki\Synonym\Pages\ViewSynonym;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Synonym;
use Filament\Facades\Filament;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\QueryBuilder\Constraints\SelectConstraint;
use Filament\QueryBuilder\Constraints\TextConstraint;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SynonymResource extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = Synonym::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.synonym');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.synonyms');
    }

    public static function getNavigationGroup(): NavigationGroup
    {
        return NavigationGroup::CONTENT;
    }

    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::OutlinedGlobeAlt;
    }

    public static function getRecordSlug(): string
    {
        return 'synonyms';
    }

    public static function getRecordTitleAttribute(): string
    {
        return Synonym::ATTRIBUTE_TEXT;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Necessary to prevent lazy loading when loading related resources
        return $query->with([Synonym::RELATION_SYNONYMABLE]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                MorphToSelect::make(Synonym::RELATION_SYNONYMABLE)
                    ->label(__('filament.fields.synonym.synonymable.name'))
                    ->hiddenOn(SynonymAnimeRelationManager::class)
                    ->types([
                        Type::make(Anime::class)
                            ->titleAttribute(Anime::ATTRIBUTE_NAME),
                    ])
                    ->required(),

                Select::make(Synonym::ATTRIBUTE_TYPE)
                    ->label(__('filament.fields.synonym.type.name'))
                    ->helperText(__('filament.fields.synonym.type.help'))
                    ->options(SynonymType::class)
                    ->required(),

                TextInput::make(Synonym::ATTRIBUTE_TEXT)
                    ->label(__('filament.fields.synonym.text.name'))
                    ->helperText(__('filament.fields.synonym.text.help'))
                    ->required()
                    ->maxLength(255),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make(Synonym::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(Synonym::RELATION_SYNONYMABLE)
                    ->hiddenOn(SynonymAnimeRelationManager::class)
                    ->color('related-link')
                    ->weight(FontWeight::SemiBold)
                    ->state(fn (Synonym $record) => $record->synonymable->getName())
                    ->url(function (Synonym $record): string {
                        $synonymable = $record->synonymable;

                        return Filament::getModelResource($synonymable)::getUrl('view', ['record' => $synonymable]);
                    }),

                TextColumn::make(Synonym::ATTRIBUTE_TYPE)
                    ->label(__('filament.fields.synonym.type.name'))
                    ->formatStateUsing(fn (SynonymType $state): ?string => $state->localize()),

                TextColumn::make(Synonym::ATTRIBUTE_TEXT)
                    ->label(__('filament.fields.synonym.text.name'))
                    ->limit(50)
                    ->tooltip(fn (string $state): string => $state),
            ])
            ->searchable();
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
                    ->schema([
                        TextEntry::make(Synonym::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(Synonym::RELATION_SYNONYMABLE)
                            ->color('related-link')
                            ->weight(FontWeight::SemiBold)
                            ->state(fn (Synonym $record) => $record->synonymable->getName())
                            ->url(function (Synonym $record): string {
                                $synonymable = $record->synonymable;

                                return Filament::getModelResource($synonymable)::getUrl('view', ['record' => $synonymable]);
                            }),

                        TextEntry::make(Synonym::ATTRIBUTE_TYPE)
                            ->label(__('filament.fields.synonym.type.name'))
                            ->formatStateUsing(fn (SynonymType $state): ?string => $state->localize()),

                        TextEntry::make(Synonym::ATTRIBUTE_TEXT)
                            ->label(__('filament.fields.synonym.text.name'))
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                TimestampSection::make(),
            ])
            ->columns(2);
    }

    /**
     * @return \Filament\Tables\Filters\BaseFilter[]
     */
    public static function getFilters(): array
    {
        return [
            QueryBuilder::make()
                ->constraints([
                    TextConstraint::make(Synonym::ATTRIBUTE_TEXT)
                        ->label(__('filament.fields.synonym.text.name')),

                    SelectConstraint::make(Synonym::ATTRIBUTE_TYPE)
                        ->label(__('filament.fields.synonym.type.name'))
                        ->options(SynonymType::class)
                        ->multiple(),

                    ...parent::getConstraints(),
                ]),

            ...parent::getFilters(),
        ];
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
            'index' => ListSynonyms::route('/'),
            'view' => ViewSynonym::route('/{record:synonym_id}'),
        ];
    }
}
