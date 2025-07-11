<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Wiki\Video\Script;

use App\Actions\Storage\Wiki\Video\Script\DeleteScriptAction as DeleteScript;
use App\Filament\Actions\Storage\Base\DeleteAction;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

/**
 * Class DeleteScriptAction.
 */
class DeleteScriptAction extends DeleteAction
{
    /**
     * The default name of the action.
     *
     * @return string|null
     */
    public static function getDefaultName(): ?string
    {
        return 'delete-script';
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

        $this->visible(Gate::allows('delete', VideoScript::class));
    }

    /**
     * Get the underlying storage action.
     *
     * @param  VideoScript  $script
     * @param  array  $fields
     * @return DeleteScript
     */
    protected function storageAction(?Model $script, array $fields): DeleteScript
    {
        return new DeleteScript($script);
    }
}
