<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\User;

use Filament\Schemas\Schema;
use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\User\Report\ReportStep as ReportStepResource;
use App\Models\User\Report\ReportStep;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ReportStepRelationManager.
 */
abstract class ReportStepRelationManager extends BaseRelationManager
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
        return ReportStepResource::form($schema);
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
                ->modifyQueryUsing(fn (Builder $query) => $query->with(ReportStepResource::getEloquentQuery()->getEagerLoads()))
                ->heading(ReportStepResource::getPluralLabel())
                ->modelLabel(ReportStepResource::getLabel())
                ->recordTitleAttribute(ReportStepResource::getRecordTitleAttribute())
                ->columns(ReportStepResource::table($table)->getColumns())
                ->defaultSort(ReportStep::TABLE . '.' . ReportStep::ATTRIBUTE_ID, 'desc')
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
            ...ReportStepResource::getFilters(),
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
            ...ReportStepResource::getRecordActions(),
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
            ...ReportStepResource::getBulkActions(),
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
            ...ReportStepResource::getTableActions(),
        ];
    }
}
