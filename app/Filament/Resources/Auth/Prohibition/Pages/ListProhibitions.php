<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth\Prohibition\Pages;

use App\Filament\Resources\Auth\ProhibitionResource;
use App\Filament\Resources\Base\BaseListResources;

class ListProhibitions extends BaseListResources
{
    protected static string $resource = ProhibitionResource::class;
}
