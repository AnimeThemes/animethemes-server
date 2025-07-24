<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth\Role\Pages;

use App\Filament\Resources\Auth\Role;
use App\Filament\Resources\Base\BaseListResources;

class ListRoles extends BaseListResources
{
    protected static string $resource = Role::class;
}
