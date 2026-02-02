<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Base;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Admin\ActionLogResource;
use App\Filament\Resources\BaseResource;
use App\Models\Admin\ActionLog;
use Filament\Tables\Table;

class ActionLogRelationManager extends BaseRelationManager
{
    protected static string $relationship = 'actionlogs';

    protected static ?string $recordTitleAttribute = ActionLog::ATTRIBUTE_ID;

    /**
     * The resource of the relation manager.
     *
     * @var class-string<BaseResource>|null
     */
    protected static ?string $relatedResource = ActionLogResource::class;

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->defaultSort(ActionLog::ATTRIBUTE_ID, 'desc')
            ->defaultPaginationPageOption(5);
    }

    public function canCreate(): bool
    {
        return false;
    }
}
