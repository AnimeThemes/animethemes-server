<?php

declare(strict_types=1);

namespace App\Filament\BulkActions\Storage\Wiki\Video\Script;

use App\Actions\Storage\Wiki\Video\Script\DeleteScriptAction as DeleteScript;
use App\Filament\BulkActions\Storage\Base\DeleteBulkAction;
use App\Models\BaseModel;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Support\Facades\Gate;

class DeleteScriptBulkAction extends DeleteBulkAction
{
    /**
     * The default name of the action.
     */
    public static function getDefaultName(): ?string
    {
        return 'remove-script-bulk';
    }

    /**
     * Initial setup for the action.
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
     * @param  array<string, mixed>  $data
     * @return DeleteScript
     */
    protected function storageAction(BaseModel $script, array $data): DeleteScript
    {
        return new DeleteScript($script);
    }
}
