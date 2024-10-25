<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Models\Auth\Role;

use App\Concerns\Filament\Actions\Models\Auth\Role\GivePermissionActionTrait;
use App\Filament\HeaderActions\BaseHeaderAction;

/**
 * Class GivePermissionHeaderAction.
 */
class GivePermissionHeaderAction extends BaseHeaderAction
{
    use GivePermissionActionTrait;
}
