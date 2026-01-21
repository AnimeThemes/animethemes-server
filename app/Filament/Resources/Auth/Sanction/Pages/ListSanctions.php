<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth\Sanction\Pages;

use App\Filament\Resources\Auth\Sanction;
use App\Filament\Resources\Base\BaseListResources;

class ListSanctions extends BaseListResources
{
    protected static string $resource = Sanction::class;
}
