<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\User;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\User\Report\ReportStep as ReportStepResource;
use App\Models\User\Report\ReportStep;
use Filament\Tables\Table;

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
     */
    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->recordTitleAttribute(ReportStepResource::getRecordTitleAttribute())
                ->defaultSort(ReportStep::TABLE.'.'.ReportStep::ATTRIBUTE_ID, 'desc')
        );
    }
}
