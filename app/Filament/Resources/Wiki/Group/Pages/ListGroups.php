<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Group\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Wiki\GroupResource;

class ListGroups extends BaseListResources
{
    protected static string $resource = GroupResource::class;
}
