<?php

declare(strict_types=1);

namespace App\Concerns\Repositories\Admin;

use App\Actions\Repositories\Admin\Dump\ReconcileDumpRepositoriesAction;
use App\Actions\Repositories\ReconcileRepositoriesAction;
use App\Contracts\Repositories\RepositoryInterface;
use App\Repositories\Eloquent\Admin\DumpRepository as DumpDestinationRepository;
use App\Repositories\Storage\Admin\DumpRepository as DumpSourceRepository;
use Illuminate\Support\Facades\App;

trait ReconcilesDumpRepositories
{
    /**
     * Get source repository for action.
     *
     * @param  array  $data
     */
    protected function getSourceRepository(array $data = []): ?RepositoryInterface
    {
        return App::make(DumpSourceRepository::class);
    }

    /**
     * Get destination repository for action.
     *
     * @param  array  $data
     */
    protected function getDestinationRepository(array $data = []): ?RepositoryInterface
    {
        return App::make(DumpDestinationRepository::class);
    }

    /**
     * Get the reconcile action.
     */
    protected function reconcileAction(): ReconcileRepositoriesAction
    {
        return new ReconcileDumpRepositoriesAction();
    }
}
