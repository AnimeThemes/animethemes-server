<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Base;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Admin\ActionLog;
use App\Filament\Resources\BaseResource;
use App\Models\Admin\ActionLog as ActionLogModel;
use Filament\Tables\Table;

class ActionLogRelationManager extends BaseRelationManager
{
    protected static string $relationship = 'actionlogs';

    protected static ?string $recordTitleAttribute = ActionLogModel::ATTRIBUTE_ID;

    /**
     * The resource of the relation manager.
     *
     * @var class-string<BaseResource>|null
     */
    protected static ?string $relatedResource = ActionLog::class;

    /**
     * The index page of the resource.
     */
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->defaultSort(ActionLogModel::ATTRIBUTE_ID, 'desc')
            ->defaultPaginationPageOption(5);
    }

    /**
     * Determine whether the related model can be created.
     *
     * @return bool
     */
    public function canCreate(): bool
    {
        return false;
    }
}
