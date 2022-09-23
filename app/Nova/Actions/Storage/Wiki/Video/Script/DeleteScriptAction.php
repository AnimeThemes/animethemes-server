<?php

declare(strict_types=1);

namespace App\Nova\Actions\Storage\Wiki\Video\Script;

use App\Actions\Storage\Wiki\Video\Script\DeleteScriptAction as DeleteScript;
use App\Models\Wiki\Video\VideoScript;
use App\Nova\Actions\Storage\Base\DeleteAction;
use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;

/**
 * Class DeleteScriptAction.
 */
class DeleteScriptAction extends DeleteAction
{
    /**
     * Get the displayable name of the action.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function name(): string
    {
        return __('nova.actions.video_script.delete.name');
    }

    /**
     * Get the underlying storage action.
     *
     * @param  ActionFields  $fields
     * @param  Collection<int, VideoScript>  $models
     * @return DeleteScript
     */
    protected function action(ActionFields $fields, Collection $models): DeleteScript
    {
        $script = $models->first();

        return new DeleteScript($script);
    }
}
