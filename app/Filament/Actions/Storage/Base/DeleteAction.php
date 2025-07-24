<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Base;

use App\Actions\Storage\Base\DeleteAction as BaseDeleteAction;
use App\Contracts\Actions\Storage\StorageAction as StorageActionContract;
use App\Filament\Actions\Storage\StorageAction;
use Illuminate\Database\Eloquent\Model;

abstract class DeleteAction extends StorageAction
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
     * @return BaseDeleteAction
     */
    abstract protected function storageAction(?Model $record, array $data): StorageActionContract;
}
