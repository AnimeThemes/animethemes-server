<?php

declare(strict_types=1);

namespace App\Filament\BulkActions\Storage\Wiki\Video\Script;

use App\Actions\Storage\Wiki\Video\Script\DeleteScriptAction as DeleteScript;
use App\Filament\BulkActions\Storage\Base\DeleteBulkAction;
use App\Models\BaseModel;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Support\Facades\Gate;

/**
 * Class DeleteScriptBulkAction.
 */
class DeleteScriptBulkAction extends DeleteBulkAction
{
    /**
     * The default name of the action.
     *
     * @return string|null
     */
    public static function getDefaultName(): ?string
    {
        return 'remove-script-bulk';
    }

    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.video_script.delete.name'));

        $this->visible(Gate::allows('forceDeleteAny', VideoScript::class));
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
