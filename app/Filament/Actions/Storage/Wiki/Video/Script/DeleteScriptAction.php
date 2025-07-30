<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Wiki\Video\Script;

use App\Actions\Storage\Wiki\Video\Script\DeleteScriptAction as DeleteScript;
use App\Filament\Actions\Storage\Base\DeleteAction;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class DeleteScriptAction extends DeleteAction
{
    /**
     * The default name of the action.
     */
    public static function getDefaultName(): ?string
    {
        return 'delete-script';
    }

    /**
     * Initial setup for the action.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.video_script.delete.name'));

        $this->visible(Gate::allows('deleteAny', VideoScript::class));
    }

    /**
     * Get the underlying storage action.
     *
     * @param  VideoScript  $script
     * @param  array<string, mixed>  $data
     */
    protected function storageAction(?Model $script, array $data): DeleteScript
    {
        return new DeleteScript($script);
    }
}
