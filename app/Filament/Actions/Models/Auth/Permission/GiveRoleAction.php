<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Auth\Permission;

use App\Concerns\Filament\Actions\Models\Auth\Permission\GiveRoleActionTrait;
use App\Filament\Actions\BaseAction;

/**
 * Class GiveRoleAction.
 */
class GiveRoleAction extends BaseAction
{
    use GiveRoleActionTrait;
}
