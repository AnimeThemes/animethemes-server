<?php

declare(strict_types=1);

namespace App\Filament\Resources\User\Submission\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\User\Submission;

class ListSubmissions extends BaseListResources
{
    protected static string $resource = Submission::class;
}
