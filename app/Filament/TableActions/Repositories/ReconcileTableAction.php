<?php

declare(strict_types=1);

namespace App\Filament\TableActions\Repositories;

use App\Concerns\Repositories\ReconcilesRepositories;
use App\Models\BaseModel;
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

      $this->action(fn (BaseModel $record, array $data) => $this->handle($record, $data));
    }

    /**
     * Perform the action on the given models.
     *
     * @param  BaseModel  $record
     * @param  array  $fields
     * @return void
     *
     * @throws Exception
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function handle(BaseModel $record, array $fields): void
    {
      $result = $this->reconcileRepositories($fields);

      $result->toLog();
    }
}
