<?php

declare(strict_types=1);

namespace App\Filament\Resources\List\External;

use App\Enums\Filament\NavigationGroup;
use App\Enums\Models\List\ExternalEntryWatchStatus;
use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\BelongsTo;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Fields\TextInput;
use App\Filament\Components\Infolist\BelongsToEntry;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\List\External\ExternalEntry\Pages\ListExternalEntries;
use App\Filament\Resources\List\External\ExternalEntry\Pages\ViewExternalEntry;
use App\Filament\Resources\List\External\RelationManagers\ExternalEntryExternalProfileRelationManager;
use App\Filament\Resources\List\ExternalProfile as ExternalProfileResource;
use App\Filament\Resources\Wiki\Anime;
use App\Models\List\External\ExternalEntry as ExternalEntryModel;
use Filament\Forms\Components\Checkbox;
use Filament\Infolists\Components\IconEntry;
use Filament\QueryBuilder\Constraints\BooleanConstraint;
use Filament\QueryBuilder\Constraints\NumberConstraint;
use Filament\QueryBuilder\Constraints\SelectConstraint;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ExternalEntry extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = ExternalEntryModel::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.external_entry');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.external_entries');
    }

    public static function getNavigationGroup(): NavigationGroup
    {
        return NavigationGroup::LIST;
    }

    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::OutlinedQueueList;
    }

    public static function getRecordSlug(): string
    {
        return 'external-entries';
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Necessary to prevent lazy loading when loading related resources
        return $query->with([
            ExternalEntryModel::RELATION_PROFILE,
            ExternalEntryModel::RELATION_ANIME,
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                BelongsTo::make(ExternalEntryModel::ATTRIBUTE_PROFILE)
                    ->resource(ExternalProfileResource::class)
                    ->required()
                    ->hiddenOn([ExternalEntryExternalProfileRelationManager::class]),

                BelongsTo::make(ExternalEntryModel::ATTRIBUTE_ANIME)
                    ->resource(Anime::class)
                    ->required(),

                TextInput::make(ExternalEntryModel::ATTRIBUTE_SCORE)
                    ->label(__('filament.fields.external_entry.score.name'))
                    ->helperText(__('filament.fields.external_entry.score.help'))
                    ->numeric(),

                Select::make(ExternalEntryModel::ATTRIBUTE_WATCH_STATUS)
                    ->label(__('filament.fields.external_entry.watch_status.name'))
                    ->helperText(__('filament.fields.external_entry.watch_status.help'))
                    ->options(ExternalEntryWatchStatus::class)
                    ->required(),

                Checkbox::make(ExternalEntryModel::ATTRIBUTE_IS_FAVORITE)
                    ->label(__('filament.fields.external_entry.is_favorite.name'))
                    ->helperText(__('filament.fields.external_entry.is_favorite.help'))
                    ->rules(['boolean']),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                BelongsToColumn::make(ExternalEntryModel::RELATION_PROFILE, ExternalProfileResource::class),

                BelongsToColumn::make(ExternalEntryModel::RELATION_ANIME, Anime::class),

                IconColumn::make(ExternalEntryModel::ATTRIBUTE_IS_FAVORITE)
                    ->label(__('filament.fields.external_entry.is_favorite.name'))
                    ->boolean(),

                TextColumn::make(ExternalEntryModel::ATTRIBUTE_SCORE)
                    ->label(__('filament.fields.external_entry.score.name')),

                TextColumn::make(ExternalEntryModel::ATTRIBUTE_WATCH_STATUS)
                    ->label(__('filament.fields.external_entry.watch_status.name'))
                    ->formatStateUsing(fn (ExternalEntryWatchStatus $state): ?string => $state->localize()),

                TextColumn::make(ExternalEntryModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
                    ->schema([
                        BelongsToEntry::make(ExternalEntryModel::RELATION_PROFILE, ExternalProfileResource::class, true),

                        BelongsToEntry::make(ExternalEntryModel::RELATION_ANIME, Anime::class),

                        IconEntry::make(ExternalEntryModel::ATTRIBUTE_IS_FAVORITE)
                            ->label(__('filament.fields.external_entry.is_favorite.name')),

                        TextEntry::make(ExternalEntryModel::ATTRIBUTE_SCORE)
                            ->label(__('filament.fields.external_entry.score.name')),

                        TextEntry::make(ExternalEntryModel::ATTRIBUTE_WATCH_STATUS)
                            ->label(__('filament.fields.external_entry.watch_status.name'))
                            ->formatStateUsing(fn (ExternalEntryWatchStatus $state): ?string => $state->localize()),

                        TextEntry::make(ExternalEntryModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),
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
                    BooleanConstraint::make(ExternalEntryModel::ATTRIBUTE_IS_FAVORITE)
                        ->label(__('filament.fields.external_entry.is_favorite.name')),

                    NumberConstraint::make(ExternalEntryModel::ATTRIBUTE_SCORE)
                        ->label(__('filament.fields.external_entry.score.name')),

                    SelectConstraint::make(ExternalEntryModel::ATTRIBUTE_WATCH_STATUS)
                        ->label(__('filament.fields.external_entry.watch_status.name')),

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
            'index' => ListExternalEntries::route('/'),
            'view' => ViewExternalEntry::route('/{record:entry_id}'),
        ];
    }
}
