<?php

declare(strict_types=1);

namespace App\Concerns\Repositories\Wiki\Video;

use App\Actions\Repositories\ReconcileRepositoriesAction;
use App\Actions\Repositories\Wiki\Video\Script\ReconcileScriptRepositoriesAction;
use App\Contracts\Repositories\RepositoryInterface;
use App\Repositories\Eloquent\Wiki\Video\ScriptRepository as ScriptDestinationRepository;
use App\Repositories\Storage\Wiki\Video\ScriptRepository as ScriptSourceRepository;
use Illuminate\Support\Facades\App;

/**
 * Trait ReconcilesScriptRepositories.
 */
trait ReconcilesScriptRepositories
{
    /**
     * Get source repository for action.
     *
     * @param  array  $data
     * @return RepositoryInterface|null
     */
    protected function getSourceRepository(array $data = []): ?RepositoryInterface
    {
        return App::make(ScriptSourceRepository::class);
    }

    /**
     * Get destination repository for action.
     *
     * @param  array  $data
     * @return RepositoryInterface|null
     */
    protected function getDestinationRepository(array $data = []): ?RepositoryInterface
    {
        return App::make(ScriptDestinationRepository::class);
    }

    /**
     * Get the reconcile action.
     *
     * @return ReconcileRepositoriesAction
     */
    protected function reconcileAction(): ReconcileRepositoriesAction
    {
        return new ReconcileScriptRepositoriesAction();
    }
}
