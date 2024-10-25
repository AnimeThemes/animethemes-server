<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Auth\Role;

use App\Concerns\Filament\Actions\Models\Auth\Role\RevokePermissionActionTrait;
use App\Filament\Actions\BaseAction;

/**
 * Class RevokePermissionAction.
 */
class RevokePermissionAction extends BaseAction
{
    use RevokePermissionActionTrait;
}
