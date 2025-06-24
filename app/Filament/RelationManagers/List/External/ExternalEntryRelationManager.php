<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\List\External;

use Filament\Schemas\Schema;
use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\List\External\ExternalEntry as ExternalEntryResource;
use App\Models\List\External\ExternalEntry;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ExternalEntryRelationManager.
 */
abstract class ExternalEntryRelationManager extends BaseRelationManager
{
    /**
     * The form to the actions.
     *
     * @param  Schema  $schema
     * @return Schema
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function form(Schema $schema): Schema
    {
        return ExternalEntryResource::form($schema);
    }

    /**
     * The index page of the resource.
     *
     * @param  Table  $table
     * @return Table
     */
    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->modifyQueryUsing(fn (Builder $query) => $query->with(ExternalEntryResource::getEloquentQuery()->getEagerLoads()))
                ->heading(ExternalEntryResource::getPluralLabel())
                ->modelLabel(ExternalEntryResource::getLabel())
                ->recordTitleAttribute(ExternalEntry::ATTRIBUTE_ID)
                ->columns(ExternalEntryResource::table($table)->getColumns())
                ->defaultSort(ExternalEntry::TABLE.'.'.ExternalEntry::ATTRIBUTE_ID, 'desc')
        );
    }

    /**
     * Get the filters available for the relation.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getFilters(): array
    {
        return [
            ...ExternalEntryResource::getFilters(),
        ];
    }

    /**
     * Get the actions available for the relation.
     *
     * @return array
     */
    public static function getRecordActions(): array
    {
        return [
            ...parent::getRecordActions(),
            ...ExternalEntryResource::getRecordActions(),
        ];
    }

    /**
     * Get the bulk actions available for the relation.
     *
     * @param  array|null  $actionsIncludedInGroup
     * @return array
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return [
            ...parent::getBulkActions(),
            ...ExternalEntryResource::getBulkActions(),
        ];
    }

    /**
     * Get the header actions available for the relation.
     * These are merged with the table actions of the resources.
     *
     * @return array
     */
    public static function getHeaderActions(): array
    {
        return [
            ...parent::getHeaderActions(),
            ...ExternalEntryResource::getTableActions(),
        ];
    }
}
