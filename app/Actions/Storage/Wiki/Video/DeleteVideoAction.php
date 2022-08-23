<?php

declare(strict_types=1);

namespace App\Actions\Storage\Wiki\Video;

use App\Actions\Repositories\ReconcileRepositories;
use App\Actions\Repositories\Wiki\Video\ReconcileVideoRepositories;
use App\Actions\Storage\Base\DeleteAction;
use App\Constants\Config\VideoConstants;
use App\Contracts\Repositories\RepositoryInterface;
use App\Models\Wiki\Video;
use App\Repositories\Eloquent\Wiki\VideoRepository as VideoDestinationRepository;
use App\Repositories\Storage\Wiki\VideoRepository as VideoSourceRepository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

/**
 * Class DeleteVideoAction.
 *
 * @extends DeleteAction<Video>
 */
class DeleteVideoAction extends DeleteAction
{
    /**
     * Create a new action instance.
     *
     * @param  Video  $video
     */
    public function __construct(Video $video)
    {
        parent::__construct($video);
    }

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
        $sourceRepository->handleFilter('path', File::dirname($this->path()));
        $destinationRepository->handleFilter('path', File::dirname($this->path()));
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

    /**
     * Get the path to delete.
     *
     * @return string
     */
    protected function path(): string
    {
        return $this->model->path();
    }
}
