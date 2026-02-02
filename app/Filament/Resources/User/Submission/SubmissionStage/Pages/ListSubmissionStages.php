<?php

declare(strict_types=1);

namespace App\Filament\Resources\User\Submission\SubmissionStage\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\User\Submission\SubmissionStageResource;

class ListSubmissionStages extends BaseListResources
{
    protected static string $resource = SubmissionStageResource::class;
}
