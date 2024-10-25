<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Storage\Wiki\Video;

use App\Concerns\Filament\Actions\Storage\Wiki\Video\MoveVideoActionTrait;
use App\Filament\HeaderActions\Storage\Base\MoveHeaderAction;

/**
 * Class MoveVideoHeaderAction.
 */
class MoveVideoHeaderAction extends MoveHeaderAction
{
    use MoveVideoActionTrait;
}
