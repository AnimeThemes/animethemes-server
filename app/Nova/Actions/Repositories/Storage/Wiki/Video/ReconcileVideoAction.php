<?php

declare(strict_types=1);

namespace App\Nova\Actions\Repositories\Storage\Wiki\Video;

use App\Concerns\Repositories\Wiki\ReconcilesVideoRepositories;
use App\Constants\Config\VideoConstants;
use App\Nova\Actions\Repositories\Storage\ReconcileStorageAction;
use Illuminate\Support\Facades\Config;

/**
 * Class ReconcileVideoAction.
 */
class ReconcileVideoAction extends ReconcileStorageAction
{
    use ReconcilesVideoRepositories;

    /**
     * Get the displayable name of the action.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function name(): string
    {
        return __('nova.actions.repositories.name', ['label' => __('nova.resources.label.videos')]);
    }

    /**
     * The name of the disk.
     *
     * @return string
     */
    public function disk(): string
    {
        return Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED);
    }
}
