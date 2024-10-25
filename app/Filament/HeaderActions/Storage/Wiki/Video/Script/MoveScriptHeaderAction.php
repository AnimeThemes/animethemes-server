<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Storage\Wiki\Video\Script;

use App\Concerns\Filament\Actions\Storage\Wiki\Video\Script\MoveScriptActionTrait;
use App\Filament\HeaderActions\Storage\Base\MoveHeaderAction;

/**
 * Class MoveScriptHeaderAction.
 */
class MoveScriptHeaderAction extends MoveHeaderAction
{
    use MoveScriptActionTrait;
}
