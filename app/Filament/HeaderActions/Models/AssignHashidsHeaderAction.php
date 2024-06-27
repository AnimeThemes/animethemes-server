<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Models;

use App\Actions\Models\AssignHashidsAction as AssignHashids;
use App\Contracts\Models\HasHashids;
use App\Filament\HeaderActions\BaseHeaderAction;
use App\Models\BaseModel;
use App\Models\List\Playlist;
use Exception;

/**
 * Class AssignHashidsHeaderAction.
 */
class AssignHashidsHeaderAction extends BaseHeaderAction
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

        $this->label(__('filament.actions.models.assign_hashids.name'));

        $this->authorize('update', Playlist::class);

        $this->action(fn (BaseModel $record) => $this->handle($record));
    }

    /**
     * Perform the action on the given models.
     *
     * @param  BaseModel  $model
     * @return void
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function handle(BaseModel $model): void
    {
        $action = new AssignHashids();

        if ($model instanceof HasHashids) {
            try {
                $action->assign($model, $this->connection);
            } catch (Exception $e) {
                $this->failedLog($e);
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
