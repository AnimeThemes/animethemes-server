<?php

declare(strict_types=1);

namespace App\Filament\Resources\User\Report\RelationManagers;

use App\Filament\RelationManagers\User\ReportStepRelationManager;
use App\Models\User\Report;
use App\Models\User\Report\ReportStep;
use Filament\Tables\Table;

class StepReportRelationManager extends ReportStepRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = Report::RELATION_STEPS;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(ReportStep::RELATION_REPORT)
        );
    }
}
