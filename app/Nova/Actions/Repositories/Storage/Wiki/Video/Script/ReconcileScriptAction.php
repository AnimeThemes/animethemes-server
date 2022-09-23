<?php

declare(strict_types=1);

namespace App\Nova\Actions\Repositories\Storage\Wiki\Video\Script;

use App\Concerns\Repositories\Wiki\Video\ReconcilesScriptRepositories;
use App\Constants\Config\VideoConstants;
use App\Nova\Actions\Repositories\Storage\ReconcileStorageAction;
use Illuminate\Support\Facades\Config;

/**
 * Class ReconcileScriptAction.
 */
class ReconcileScriptAction extends ReconcileStorageAction
{
    use ReconcilesScriptRepositories;

    /**
     * Get the displayable name of the action.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function name(): string
    {
        return __('nova.actions.repositories.name', ['label' => __('nova.resources.label.video_scripts')]);
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
}
