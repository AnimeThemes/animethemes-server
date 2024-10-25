<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Auth\User;

use App\Concerns\Filament\Actions\Models\Auth\User\RevokePermissionActionTrait;
use App\Filament\Actions\BaseAction;

/**
 * Class RevokePermissionAction.
 */
class RevokePermissionAction extends BaseAction
{
    use RevokePermissionActionTrait;
}
