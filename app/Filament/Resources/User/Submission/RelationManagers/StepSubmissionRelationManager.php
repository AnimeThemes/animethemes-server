<?php

declare(strict_types=1);

namespace App\Filament\Resources\User\Submission\RelationManagers;

use App\Filament\RelationManagers\User\SubmissionStepRelationManager;
use App\Models\User\Submission;
use App\Models\User\Submission\SubmissionStep;
use Filament\Tables\Table;

class StepSubmissionRelationManager extends SubmissionStepRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = Submission::RELATION_STEPS;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(SubmissionStep::RELATION_SUBMISSION)
        );
    }
}
