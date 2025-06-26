<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth\Permission\Pages;

use App\Filament\Resources\Auth\Permission;
use App\Filament\Resources\Base\BaseListResources;

/**
 * Class ListPermissions.
 */
class ListPermissions extends BaseListResources
{
    protected static string $resource = Permission::class;
}
