<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Storage\Wiki\Image;

use App\Concerns\Filament\Actions\Storage\Wiki\Image\MoveImageActionTrait;
use App\Filament\HeaderActions\Storage\Base\MoveHeaderAction;

/**
 * Class MoveImageHeaderAction.
 */
class MoveImageHeaderAction extends MoveHeaderAction
{
    use MoveImageActionTrait;
}
