<?php

declare(strict_types=1);

namespace App\Filament\Actions\Repositories;

use App\Concerns\Repositories\ReconcilesRepositories;
use App\Filament\Actions\BaseAction;
use Exception;

/**
 * Class ReconcileAction.
 */
abstract class ReconcileAction extends BaseAction
{
    use ReconcilesRepositories;

    /**
     * Perform the action on the given models.
     *
     * @param  array<string, mixed>  $data
     * @return void
     *
     * @throws Exception
     */
    public function handle(array $data): void
    {
        $result = $this->reconcileRepositories($data);

        $result->toLog();
    }
}
