<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Wiki\Image;

use App\Concerns\Filament\Actions\Storage\Wiki\Image\MoveImageActionTrait;
use App\Filament\Actions\Storage\Base\MoveAction;

/**
 * Class MoveImageAction.
 */
class MoveImageAction extends MoveAction
{
    use MoveImageActionTrait;
}
