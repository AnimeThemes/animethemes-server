<?php

declare(strict_types=1);

namespace App\Nova\Actions\Wiki\Video;

use App\Actions\Repositories\ReconcileRepositories;
use App\Actions\Repositories\Wiki\Video\ReconcileVideoRepositories;
use App\Constants\Config\VideoConstants;
use App\Contracts\Repositories\RepositoryInterface;
use App\Nova\Actions\Wiki\ReconcileStorageAction;
use App\Repositories\Eloquent\Wiki\VideoRepository as VideoDestinationRepository;
use App\Repositories\Storage\Wiki\VideoRepository as VideoSourceRepository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Laravel\Nova\Fields\ActionFields;

/**
 * Class ReconcileVideoAction.
 */
class ReconcileVideoAction extends ReconcileStorageAction
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
        return __('nova.reconcile_videos');
    }

    /**
     * Get the name of the disk that represents the filesystem.
     *
     * @return string
     */
    protected function disk(): string
    {
        return Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED);
    }

    /**
     * Get source repository for action.
     *
     * @param  ActionFields  $fields
     * @return RepositoryInterface|null
     */
    protected function getSourceRepository(ActionFields $fields): ?RepositoryInterface
    {
        return App::make(VideoSourceRepository::class);
    }

    /**
     * Get destination repository for action.
     *
     * @param  ActionFields  $fields
     * @return RepositoryInterface|null
     */
    protected function getDestinationRepository(ActionFields $fields): ?RepositoryInterface
    {
        return App::make(VideoDestinationRepository::class);
    }

    /**
     * Get the reconciliation action.
     *
     * @return ReconcileRepositories
     */
    protected function getAction(): ReconcileRepositories
    {
        return new ReconcileVideoRepositories();
    }
}
