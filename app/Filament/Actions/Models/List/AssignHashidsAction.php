<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\List;

use App\Actions\Models\AssignHashidsAction as AssignHashids;
use App\Contracts\Models\HasHashids;
use App\Filament\Actions\BaseAction;
use App\Models\BaseModel;
use App\Models\List\Playlist;
use Illuminate\Support\Facades\Gate;

class AssignHashidsAction extends BaseAction
{
    protected ?string $connection = null;

    public static function getDefaultName(): ?string
    {
        return 'assign-hashids';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.models.list.assign_hashids.name'));

        $this->visible(Gate::allows('create', Playlist::class));

        $this->action(fn (BaseModel $record) => $this->handle($record));
    }

    public function handle(BaseModel $model): void
    {
        $action = new AssignHashids();

        if ($model instanceof HasHashids) {
            $action->assign($model, $this->connection);
        }
    }

    /**
     * Set the connection.
     */
    public function setConnection(?string $connection): static
    {
        $this->connection = $connection;

        return $this;
    }
}
