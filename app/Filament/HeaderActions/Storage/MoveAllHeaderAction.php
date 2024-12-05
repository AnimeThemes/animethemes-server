<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Storage;

use App\Concerns\Filament\Actions\Storage\MoveAllActionTrait;
use App\Filament\HeaderActions\BaseHeaderAction;

/**
 * Class MoveAllHeaderAction.
 */
class MoveAllHeaderAction extends BaseHeaderAction
{
    use MoveAllActionTrait;
}
