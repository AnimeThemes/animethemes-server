<?php

declare(strict_types=1);

namespace App\Filament\BulkActions\Storage\Base;

use App\Actions\Storage\Base\DeleteAction as BaseDeleteAction;
use App\Filament\BulkActions\Storage\StorageBulkAction;
use App\Models\BaseModel;

abstract class DeleteBulkAction extends StorageBulkAction
{
    /**
     * Initial setup for the action.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->color('danger');

        $this->icon(__('filament-icons.actions.base.delete'));
    }

    /**
     * Get the underlying storage action.
     *
     * @param  array<string, mixed>  $data
     */
    abstract protected function storageAction(BaseModel $model, array $data): BaseDeleteAction;
}
