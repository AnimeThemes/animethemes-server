<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Storage\Wiki\Video\Script;

use App\Actions\Storage\Wiki\Video\Script\DeleteScriptAction as DeleteScript;
use App\Filament\HeaderActions\Storage\Base\DeleteHeaderAction;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DeleteScriptHeaderAction.
 */
class DeleteScriptHeaderAction extends DeleteHeaderAction
{
    /**
     * Get the underlying storage action.
     *
     * @param  Model  $script
     * @param  array  $fields
     * @return DeleteScript
     */
    protected function storageAction(Model $script, array $fields): DeleteScript
    {
        return new DeleteScript($script);
    }
}
