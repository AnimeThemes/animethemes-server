<?php

declare(strict_types=1);

namespace App\Filament\TableActions\Repositories;

use App\Concerns\Repositories\ReconcilesRepositories;
use App\Filament\TableActions\BaseTableAction;
use Exception;

/**
 * Class ReconcileTableAction.
 */
abstract class ReconcileTableAction extends BaseTableAction
{
    use ReconcilesRepositories;

    /**
     * Perform the action on the given models.
     *
     * @param  array  $fields
     * @return void
     *
     * @throws Exception
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function handle(array $fields): void
    {
      $result = $this->reconcileRepositories($fields);

      $result->toLog();
    }
}
