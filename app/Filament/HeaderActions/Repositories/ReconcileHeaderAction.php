<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Repositories;

use App\Concerns\Repositories\ReconcilesRepositories;
use Exception;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ReconcileHeaderAction.
 */
abstract class ReconcileHeaderAction extends Action
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

      $this->action(fn (Model $record, array $data) => $this->handle($record, $data));
    }

    /**
     * Perform the action on the given models.
     *
     * @param  Model  $record
     * @param  array  $fields
     * @return void
     *
     * @throws Exception
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function handle(Model $record, array $fields): void
    {
      $result = $this->reconcileRepositories($fields);

      $result->toLog();
    }
}
