<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Auth\Role;

use App\Concerns\Filament\Actions\Models\Auth\Role\GivePermissionActionTrait;
use App\Filament\Actions\BaseAction;

/**
 * Class GivePermissionAction.
 */
class GivePermissionAction extends BaseAction
{
    use GivePermissionActionTrait;
}
