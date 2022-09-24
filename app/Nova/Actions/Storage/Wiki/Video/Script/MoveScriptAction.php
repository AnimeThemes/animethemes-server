<?php

declare(strict_types=1);

namespace App\Nova\Actions\Storage\Wiki\Video\Script;

use App\Actions\Storage\Wiki\Video\Script\MoveScriptAction as MoveScript;
use App\Constants\Config\VideoConstants;
use App\Models\Wiki\Video\VideoScript;
use App\Nova\Actions\Storage\Base\MoveAction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class MoveScriptAction.
 */
class MoveScriptAction extends MoveAction
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
        return __('nova.actions.video_script.move.name');
    }

    /**
     * Get the underlying storage action.
     *
     * @param  ActionFields  $fields
     * @param  Collection<int, VideoScript>  $models
     * @return MoveScript
     */
    protected function action(ActionFields $fields, Collection $models): MoveScript
    {
        $path = $fields->get('path');

        $script = $models->first();

        return new MoveScript($script, $path);
    }

    /**
     * The name of the disk.
     *
     * @return string
     */
    public function disk(): string
    {
        return Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED);
    }

    /**
     * Resolve the default value for the path field.
     *
     * @param  NovaRequest  $request
     * @return string|null
     */
    protected function defaultPath(NovaRequest $request): ?string
    {
        $script = $request->findModelQuery()->first();

        return $script instanceof VideoScript
            ? $script->path
            : null;
    }

    /**
     * The file extension that the path must end with.
     *
     * @return string
     */
    protected function allowedFileExtension(): string
    {
        return '.txt';
    }
}
