<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Auth\User;

use App\Concerns\Filament\Actions\Models\Auth\User\GiveRoleActionTrait;
use App\Filament\Actions\BaseAction;

/**
 * Class GiveRoleAction.
 */
class GiveRoleAction extends BaseAction
{
    use GiveRoleActionTrait;
}
