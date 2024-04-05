<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth\Permission\Pages;

use App\Filament\Resources\Base\BaseCreateResource;
use App\Filament\Resources\Auth\Permission;

/**
 * Class CreatePermission.
 */
class CreatePermission extends BaseCreateResource
{
    protected static string $resource = Permission::class;
}
