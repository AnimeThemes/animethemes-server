<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth\Role\Pages;

use App\Filament\Resources\Base\BaseCreateResource;
use App\Filament\Resources\Auth\Role;

/**
 * Class CreateRole.
 */
class CreateRole extends BaseCreateResource
{
    protected static string $resource = Role::class;
}
