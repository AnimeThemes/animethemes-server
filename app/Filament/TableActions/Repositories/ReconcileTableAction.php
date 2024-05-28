<?php

declare(strict_types=1);

namespace App\Filament\TableActions\Repositories;

use App\Concerns\Repositories\ReconcilesRepositories;
use Exception;
use Filament\Tables\Actions\Action;

/**
 * Class ReconcileTableAction.
 */
abstract class ReconcileTableAction extends Action
{
    use ReconcilesRepositories;

    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
      parent::setUp();

      $this->action(fn (array $data) => $this->handle($data));
    }

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
