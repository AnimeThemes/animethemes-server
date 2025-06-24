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
     * @param  array  $fields
     * @return void
     *
     * @throws Exception
     */
    public function handle(array $fields): void
    {
      $result = $this->reconcileRepositories($fields);

      $result->toLog();
    }
}
