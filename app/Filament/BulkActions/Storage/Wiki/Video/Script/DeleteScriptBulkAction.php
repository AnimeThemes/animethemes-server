<?php

declare(strict_types=1);

namespace App\Filament\BulkActions\Storage\Wiki\Video\Script;

use App\Actions\Storage\Wiki\Video\Script\DeleteScriptAction as DeleteScript;
use App\Filament\BulkActions\Storage\Base\DeleteBulkAction;
use App\Models\BaseModel;
use App\Models\Wiki\Video\VideoScript;

/**
 * Class DeleteScriptBulkAction.
 */
class DeleteScriptBulkAction extends DeleteBulkAction
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
