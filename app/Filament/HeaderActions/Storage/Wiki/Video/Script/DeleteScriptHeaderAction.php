<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Storage\Wiki\Video\Script;

use App\Concerns\Filament\Actions\Storage\Wiki\Video\Script\DeleteScriptActionTrait;
use App\Filament\HeaderActions\Storage\Base\DeleteHeaderAction;

/**
 * Class DeleteScriptHeaderAction.
 */
class DeleteScriptHeaderAction extends DeleteHeaderAction
{
    use DeleteScriptActionTrait;
}
