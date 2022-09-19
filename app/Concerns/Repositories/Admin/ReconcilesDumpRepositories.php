<?php

declare(strict_types=1);

namespace App\Concerns\Repositories\Admin;

use App\Actions\Repositories\Admin\Dump\ReconcileDumpRepositoriesAction;
use App\Actions\Repositories\ReconcileRepositoriesAction;
use App\Contracts\Repositories\RepositoryInterface;
use App\Repositories\Eloquent\Admin\DumpRepository as DumpDestinationRepository;
use App\Repositories\Storage\Admin\DumpRepository as DumpSourceRepository;
use Illuminate\Support\Facades\App;

/**
 * Trait ReconcilesDumpRepositories.
 */
trait ReconcilesDumpRepositories
{
    /**
     * Get source repository for action.
     *
     * @param  array  $data
     * @return RepositoryInterface|null
     */
    protected function getSourceRepository(array $data = []): ?RepositoryInterface
    {
        return App::make(DumpSourceRepository::class);
    }

    /**
     * Get destination repository for action.
     *
     * @param  array  $data
     * @return RepositoryInterface|null
     */
    protected function getDestinationRepository(array $data = []): ?RepositoryInterface
    {
        return App::make(DumpDestinationRepository::class);
    }

    /**
     * Get the reconcile action.
     *
     * @return ReconcileRepositoriesAction
     */
    protected function action(): ReconcileRepositoriesAction
    {
        return new ReconcileDumpRepositoriesAction();
    }
}
