<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\User;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\User\Report\ReportStep as ReportStepResource;
use App\Models\User\Report\ReportStep;
use Filament\Tables\Table;

/**
 * Class ReportStepRelationManager.
 */
abstract class ReportStepRelationManager extends BaseRelationManager
{
    /**
     * The resource of the relation manager.
     *
     * @var class-string<BaseResource>|null
     */
    protected static ?string $relatedResource = ReportStepResource::class;

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
                ->recordTitleAttribute(ReportStepResource::getRecordTitleAttribute())
                ->columns(ReportStepResource::table($table)->getColumns())
                ->defaultSort(ReportStep::TABLE.'.'.ReportStep::ATTRIBUTE_ID, 'desc')
        );
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
            ...ReportStepResource::getActions(),
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
