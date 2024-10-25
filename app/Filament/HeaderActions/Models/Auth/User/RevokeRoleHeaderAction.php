<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Models\Auth\User;

use App\Concerns\Filament\Actions\Models\Auth\User\RevokeRoleActionTrait;
use App\Filament\HeaderActions\BaseHeaderAction;

/**
 * Class RevokeRoleHeaderAction.
 */
class RevokeRoleHeaderAction extends BaseHeaderAction
{
    use RevokeRoleActionTrait;
}
