<?php

declare(strict_types=1);

namespace App\Actions\Storage\Wiki\Video;

use App\Actions\Repositories\ReconcileRepositories;
use App\Actions\Repositories\Wiki\Video\ReconcileVideoRepositories;
use App\Actions\Storage\Base\UploadAction;
use App\Constants\Config\VideoConstants;
use App\Contracts\Repositories\RepositoryInterface;
use App\Repositories\Eloquent\Wiki\VideoRepository as VideoDestinationRepository;
use App\Repositories\Storage\Wiki\VideoRepository as VideoSourceRepository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

/**
 * Class UploadVideoAction.
 */
class UploadVideoAction extends UploadAction
{
    /**
     * Get the disks to update.
     *
     * @return array
     */
    protected function disks(): array
    {
        return Config::get(VideoConstants::DISKS_QUALIFIED);
    }

    /**
     * Get source repository for action.
     *
     * @return RepositoryInterface
     */
    protected function getSourceRepository(): RepositoryInterface
    {
        return App::make(VideoSourceRepository::class);
    }

    /**
     * Get destination repository for action.
     *
     * @return RepositoryInterface
     */
    protected function getDestinationRepository(): RepositoryInterface
    {
        return App::make(VideoDestinationRepository::class);
    }

    /**
     * Apply filters to repositories before reconciliation.
     *
     * @param  RepositoryInterface  $sourceRepository
     * @param  RepositoryInterface  $destinationRepository
     * @return void
     */
    protected function handleFilters(
        RepositoryInterface $sourceRepository,
        RepositoryInterface $destinationRepository
    ): void {
        $sourceRepository->handleFilter('path', $this->path);
        $destinationRepository->handleFilter('path', $this->path);
    }

    /**
     * Get the reconcile action.
     *
     * @return ReconcileRepositories
     */
    protected function action(): ReconcileRepositories
    {
        return new ReconcileVideoRepositories();
    }
}
