<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\User;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\User\Submission\SubmissionStep as SubmissionStepResource;
use App\Models\User\Submission\SubmissionStep;
use Filament\Tables\Table;

abstract class SubmissionStepRelationManager extends BaseRelationManager
{
    /**
     * The resource of the relation manager.
     *
     * @var class-string<BaseResource>|null
     */
    protected static ?string $relatedResource = SubmissionStepResource::class;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->recordTitleAttribute(SubmissionStepResource::getRecordTitleAttribute())
                ->defaultSort(SubmissionStep::TABLE.'.'.SubmissionStep::ATTRIBUTE_ID, 'desc')
        );
    }
}
