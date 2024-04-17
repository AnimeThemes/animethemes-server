<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Models;

use App\Actions\Models\AssignHashidsAction as AssignHashids;
use App\Contracts\Models\HasHashids;
use Exception;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AssignHashidsHeaderAction.
 */
class AssignHashidsHeaderAction extends Action
{
    protected ?string $connection = null;

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
     * @param  Model  $model
     * @param  array  $data
     * @return void
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function handle(Model $model, array $data): void
    {
        $action = new AssignHashids();

        if ($model instanceof HasHashids) {
            try {
                $action->assign($model, $this->connection);
            } catch (Exception $e) {
                $this->markAsFailed($model, $e);
            }
        }
    }

    /**
     * Set the connection.
     *
     * @param  string|null  $connection
     * @return static
     */
    public function setConnection(?string $connection): static
    {
        $this->connection = $connection;

        return $this;
    }
}
