<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Models\Auth\User;

use App\Concerns\Filament\Actions\Models\Auth\User\GiveRoleActionTrait;
use App\Filament\HeaderActions\BaseHeaderAction;

/**
 * Class GiveRoleHeaderAction.
 */
class GiveRoleHeaderAction extends BaseHeaderAction
{
    use GiveRoleActionTrait;
}
