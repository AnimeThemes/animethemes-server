<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\List;

use App\Actions\Models\AssignHashidsAction as AssignHashids;
use App\Contracts\Models\HasHashids;
use App\Filament\Actions\BaseAction;
use App\Models\BaseModel;
use App\Models\List\Playlist;
use Exception;
use Illuminate\Support\Facades\Gate;

/**
 * Class AssignHashidsAction.
 */
class AssignHashidsAction extends BaseAction
{
    protected ?string $connection = null;

    /**
     * The default name of the action.
     *
     * @return string|null
     */
    public static function getDefaultName(): ?string
    {
        return 'assign-hashids';
    }

    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.models.list.assign_hashids.name'));

        $this->visible(Gate::allows('create', Playlist::class));

        $this->action(fn (BaseModel $record) => $this->handle($record));
    }

    /**
     * Perform the action on the given models.
     *
     * @param  BaseModel  $model
     * @return void
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
