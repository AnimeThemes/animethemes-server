<?php

declare(strict_types=1);

namespace App\Filament\Resources\User\Submission\SubmissionStep\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\User\Submission\SubmissionStep;

class ListSubmissionSteps extends BaseListResources
{
    protected static string $resource = SubmissionStep::class;
}
