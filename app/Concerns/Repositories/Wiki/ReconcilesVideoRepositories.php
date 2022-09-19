<?php

declare(strict_types=1);

namespace App\Concerns\Repositories\Wiki;

use App\Actions\Repositories\ReconcileRepositoriesAction;
use App\Actions\Repositories\Wiki\Video\ReconcileVideoRepositoriesAction;
use App\Contracts\Repositories\RepositoryInterface;
use App\Repositories\Eloquent\Wiki\VideoRepository as VideoDestinationRepository;
use App\Repositories\Storage\Wiki\VideoRepository as VideoSourceRepository;
use Illuminate\Support\Facades\App;

/**
 * Trait ReconcilesVideoRepositories.
 */
trait ReconcilesVideoRepositories
{
    /**
     * Get source repository for action.
     *
     * @param  array  $data
     * @return RepositoryInterface|null
     */
    protected function getSourceRepository(array $data = []): ?RepositoryInterface
    {
        return App::make(VideoSourceRepository::class);
    }

    /**
     * Get destination repository for action.
     *
     * @param  array  $data
     * @return RepositoryInterface|null
     */
    protected function getDestinationRepository(array $data = []): ?RepositoryInterface
    {
        return App::make(VideoDestinationRepository::class);
    }

    /**
     * Get the reconcile action.
     *
     * @return ReconcileRepositoriesAction
     */
    protected function action(): ReconcileRepositoriesAction
    {
        return new ReconcileVideoRepositoriesAction();
    }
}
