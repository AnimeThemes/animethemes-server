<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Group\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Wiki\Group;

class ListGroups extends BaseListResources
{
    protected static string $resource = Group::class;
}
