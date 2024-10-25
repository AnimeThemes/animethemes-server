<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Models\Auth\Role;

use App\Concerns\Filament\Actions\Models\Auth\Role\RevokePermissionActionTrait;
use App\Filament\HeaderActions\BaseHeaderAction;

/**
 * Class RevokePermissionHeaderAction.
 */
class RevokePermissionHeaderAction extends BaseHeaderAction
{
    use RevokePermissionActionTrait;
}
