<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Auth\User;

use App\Concerns\Filament\Actions\Models\Auth\User\GivePermissionActionTrait;
use App\Filament\Actions\BaseAction;
/**
 * Class GivePermissionAction.
 */
class GivePermissionAction extends BaseAction
{
    use GivePermissionActionTrait;
}
