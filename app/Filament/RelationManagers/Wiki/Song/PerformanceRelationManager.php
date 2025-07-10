<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Wiki\Song;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Song\Performance as PerformanceResource;
use App\Models\Wiki\Song\Performance;
use Filament\Tables\Table;

/**
 * Class PerformanceRelationManager.
 */
abstract class PerformanceRelationManager extends BaseRelationManager
{
    /**
     * The resource of the relation manager.
     *
     * @var class-string<BaseResource>|null
     */
    protected static ?string $relatedResource = PerformanceResource::class;

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
                ->recordTitleAttribute(Performance::ATTRIBUTE_ID)
                ->columns(PerformanceResource::table($table)->getColumns())
                ->defaultSort(Performance::TABLE.'.'.Performance::ATTRIBUTE_ID, 'desc')
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
            ...PerformanceResource::getActions(),
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
            ...PerformanceResource::getBulkActions(),
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
            ...PerformanceResource::getTableActions(),
        ];
    }
}
