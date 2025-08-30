<?php

declare(strict_types=1);

namespace App\Concerns\Repositories\Wiki\Video;

use App\Actions\Repositories\ReconcileRepositoriesAction;
use App\Actions\Repositories\Wiki\Video\Script\ReconcileScriptRepositoriesAction;
use App\Contracts\Repositories\RepositoryInterface;
use App\Repositories\Eloquent\Wiki\Video\ScriptRepository as ScriptDestinationRepository;
use App\Repositories\Storage\Wiki\Video\ScriptRepository as ScriptSourceRepository;
use Illuminate\Support\Facades\App;

trait ReconcilesScriptRepositories
{
    /**
     * @param  array  $data
     */
    protected function getSourceRepository(array $data = []): ?RepositoryInterface
    {
        return App::make(ScriptSourceRepository::class);
    }

    /**
     * @param  array  $data
     */
    protected function getDestinationRepository(array $data = []): ?RepositoryInterface
    {
        return App::make(ScriptDestinationRepository::class);
    }

    protected function reconcileAction(): ReconcileRepositoriesAction
    {
        return new ReconcileScriptRepositoriesAction();
    }
}
