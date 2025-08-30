<?php

declare(strict_types=1);

namespace App\Concerns\Repositories\Wiki;

use App\Actions\Repositories\ReconcileRepositoriesAction;
use App\Actions\Repositories\Wiki\Audio\ReconcileAudioRepositoriesAction;
use App\Contracts\Repositories\RepositoryInterface;
use App\Repositories\Eloquent\Wiki\AudioRepository as AudioDestinationRepository;
use App\Repositories\Storage\Wiki\AudioRepository as AudioSourceRepository;
use Illuminate\Support\Facades\App;

trait ReconcilesAudioRepositories
{
    /**
     * @param  array  $data
     */
    protected function getSourceRepository(array $data = []): ?RepositoryInterface
    {
        return App::make(AudioSourceRepository::class);
    }

    /**
     * @param  array  $data
     */
    protected function getDestinationRepository(array $data = []): ?RepositoryInterface
    {
        return App::make(AudioDestinationRepository::class);
    }

    protected function reconcileAction(): ReconcileRepositoriesAction
    {
        return new ReconcileAudioRepositoriesAction();
    }
}
