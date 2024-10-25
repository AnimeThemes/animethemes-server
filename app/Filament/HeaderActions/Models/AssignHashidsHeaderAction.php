<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Models;

use App\Concerns\Filament\Actions\Models\AssignHashidsActionTrait;
use App\Filament\HeaderActions\BaseHeaderAction;

/**
 * Class AssignHashidsHeaderAction.
 */
class AssignHashidsHeaderAction extends BaseHeaderAction
{
    use AssignHashidsActionTrait;
}
