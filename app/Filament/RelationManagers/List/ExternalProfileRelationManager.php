<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\List;

use Filament\Schemas\Schema;
use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\List\ExternalProfile as ExternalProfileResource;
use App\Models\List\ExternalProfile;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ExternalProfileRelationManager.
 */
abstract class ExternalProfileRelationManager extends BaseRelationManager
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
        return ExternalProfileResource::form($schema);
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
                ->modifyQueryUsing(fn (Builder $query) => $query->with(ExternalProfileResource::getEloquentQuery()->getEagerLoads()))
                ->heading(ExternalProfileResource::getPluralLabel())
                ->modelLabel(ExternalProfileResource::getLabel())
                ->recordTitleAttribute(ExternalProfile::ATTRIBUTE_NAME)
                ->columns(ExternalProfileResource::table($table)->getColumns())
                ->defaultSort(ExternalProfile::TABLE.'.'.ExternalProfile::ATTRIBUTE_ID, 'desc')
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
            ...ExternalProfileResource::getFilters(),
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
            ...ExternalProfileResource::getActions(),
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
            ...ExternalProfileResource::getBulkActions(),
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
            ...ExternalProfileResource::getTableActions(),
        ];
    }
}
