<?php

declare(strict_types=1);

namespace App\Filament\Resources\List\External;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use App\Enums\Models\List\ExternalEntryWatchStatus;
use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\BelongsTo;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Filters\DateFilter;
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
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\IconEntry;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ExternalEntry.
 */
class ExternalEntry extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = ExternalEntryModel::class;

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getLabel(): string
    {
        return __('filament.resources.singularLabel.external_entry');
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
        return __('filament.resources.label.external_entries');
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
        return __('filament.resources.group.list');
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
        return __('filament-icons.resources.external_entries');
    }

    /**
     * Get the slug (URI key) for the resource.
     *
     * @return string
     */
    public static function getRecordSlug(): string
    {
        return 'external-entries';
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
            ExternalEntryModel::RELATION_PROFILE,
            ExternalEntryModel::RELATION_ANIME,
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
                BelongsToColumn::make(ExternalEntryModel::RELATION_PROFILE, ExternalProfileResource::class),

                BelongsToColumn::make(ExternalEntryModel::RELATION_ANIME, Anime::class),

                IconColumn::make(ExternalEntryModel::ATTRIBUTE_IS_FAVORITE)
                    ->label(__('filament.fields.external_entry.is_favorite.name'))
                    ->boolean(),

                TextColumn::make(ExternalEntryModel::ATTRIBUTE_SCORE)
                    ->label(__('filament.fields.external_entry.score.name')),

                TextColumn::make(ExternalEntryModel::ATTRIBUTE_WATCH_STATUS)
                    ->label(__('filament.fields.external_entry.watch_status.name'))
                    ->formatStateUsing(fn (ExternalEntryWatchStatus $state) => $state->localize()),

                TextColumn::make(ExternalEntryModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),
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
                        BelongsToEntry::make(ExternalEntryModel::RELATION_PROFILE, ExternalProfileResource::class, true),

                        BelongsToEntry::make(ExternalEntryModel::RELATION_ANIME, Anime::class),

                        IconEntry::make(ExternalEntryModel::ATTRIBUTE_IS_FAVORITE)
                            ->label(__('filament.fields.external_entry.is_favorite.name')),

                        TextEntry::make(ExternalEntryModel::ATTRIBUTE_SCORE)
                            ->label(__('filament.fields.external_entry.score.name')),

                        TextEntry::make(ExternalEntryModel::ATTRIBUTE_WATCH_STATUS)
                            ->label(__('filament.fields.external_entry.watch_status.name'))
                            ->formatStateUsing(fn (ExternalEntryWatchStatus $state) => $state->localize()),

                        TextEntry::make(ExternalEntryModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),
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
            DateFilter::make(Model::CREATED_AT)
                ->label(__('filament.fields.base.created_at')),

            DateFilter::make(Model::UPDATED_AT)
                ->label(__('filament.fields.base.updated_at')),
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array
     */
    public static function getRecordActions(): array
    {
        return [];
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
            'index' => ListExternalEntries::route('/'),
            'view' => ViewExternalEntry::route('/{record:entry_id}'),
        ];
    }
}
