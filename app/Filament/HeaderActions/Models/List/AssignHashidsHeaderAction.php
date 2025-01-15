<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Models\List;

use App\Concerns\Filament\Actions\Models\List\AssignHashidsActionTrait;
use App\Filament\HeaderActions\BaseHeaderAction;

/**
 * Class AssignHashidsHeaderAction.
 */
class AssignHashidsHeaderAction extends BaseHeaderAction
{
    use AssignHashidsActionTrait;
}
