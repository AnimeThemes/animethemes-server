<?php

declare(strict_types=1);

namespace App\Concerns\Repositories\Wiki;

use App\Actions\Repositories\ReconcileRepositoriesAction;
use App\Actions\Repositories\Wiki\Video\ReconcileVideoRepositoriesAction;
use App\Contracts\Repositories\RepositoryInterface;
use App\Repositories\Eloquent\Wiki\VideoRepository as VideoDestinationRepository;
use App\Repositories\Storage\Wiki\VideoRepository as VideoSourceRepository;
use Illuminate\Support\Facades\App;

trait ReconcilesVideoRepositories
{
    protected function getSourceRepository(array $data = []): ?RepositoryInterface
    {
        return App::make(VideoSourceRepository::class);
    }

    protected function getDestinationRepository(array $data = []): ?RepositoryInterface
    {
        return App::make(VideoDestinationRepository::class);
    }

    protected function reconcileAction(): ReconcileRepositoriesAction
    {
        return new ReconcileVideoRepositoriesAction();
    }
}
