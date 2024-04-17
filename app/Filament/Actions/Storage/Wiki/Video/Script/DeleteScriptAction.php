<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Wiki\Video\Script;

use App\Actions\Storage\Wiki\Video\Script\DeleteScriptAction as DeleteScript;
use App\Filament\Actions\Storage\Base\DeleteAction;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DeleteScriptAction.
 */
class DeleteScriptAction extends DeleteAction
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
