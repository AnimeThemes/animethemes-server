<?php

declare(strict_types=1);

namespace App\Filament\Resources\User\Report\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\User\Report;

class ListReports extends BaseListResources
{
    protected static string $resource = Report::class;
}
