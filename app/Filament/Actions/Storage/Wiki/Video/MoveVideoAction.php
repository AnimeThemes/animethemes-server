<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Wiki\Video;

use App\Concerns\Filament\Actions\Storage\Wiki\Video\MoveVideoActionTrait;
use App\Filament\Actions\Storage\Base\MoveAction;

/**
 * Class MoveVideoAction.
 */
class MoveVideoAction extends MoveAction
{
    use MoveVideoActionTrait;
}
