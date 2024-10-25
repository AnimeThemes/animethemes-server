<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Auth\Permission;

use App\Concerns\Filament\Actions\Models\Auth\Permission\RevokeRoleActionTrait;
use App\Filament\Actions\BaseAction;

/**
 * Class RevokeRoleAction.
 */
class RevokeRoleAction extends BaseAction
{
    use RevokeRoleActionTrait;
}
