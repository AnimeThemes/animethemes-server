<?php

declare(strict_types=1);

namespace App\Filament\Resources\User\Submission\RelationManagers;

use App\Filament\RelationManagers\User\SubmissionStageRelationManager;
use App\Models\User\Submission;
use App\Models\User\Submission\SubmissionStage;
use Filament\Tables\Table;

class StageSubmissionRelationManager extends SubmissionStageRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = Submission::RELATION_STAGES;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(SubmissionStage::RELATION_SUBMISSION)
        );
    }
}
