<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Auth\User;

use App\Concerns\Filament\Actions\Models\Auth\User\RevokeRoleActionTrait;
use App\Filament\Actions\BaseAction;

/**
 * Class RevokeRoleAction.
 */
class RevokeRoleAction extends BaseAction
{
    use RevokeRoleActionTrait;
}
