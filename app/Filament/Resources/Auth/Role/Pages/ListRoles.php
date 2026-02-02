<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth\Role\Pages;

use App\Filament\Resources\Auth\RoleResource;
use App\Filament\Resources\Base\BaseListResources;

class ListRoles extends BaseListResources
{
    protected static string $resource = RoleResource::class;
}
