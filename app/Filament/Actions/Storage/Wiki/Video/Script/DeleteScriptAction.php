<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Wiki\Video\Script;

use App\Actions\Storage\Wiki\Video\Script\DeleteScriptAction as DeleteScript;
use App\Filament\Actions\Storage\Base\DeleteAction;
use App\Models\BaseModel;
use App\Models\Wiki\Video\VideoScript;

/**
 * Class DeleteScriptAction.
 */
class DeleteScriptAction extends DeleteAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.video_script.delete.name'));

        $this->authorize('delete', VideoScript::class);
    }

    /**
     * Get the underlying storage action.
     *
     * @param  VideoScript  $script
     * @param  array  $fields
     * @return DeleteScript
     */
    protected function storageAction(BaseModel $script, array $fields): DeleteScript
    {
        return new DeleteScript($script);
    }
}
