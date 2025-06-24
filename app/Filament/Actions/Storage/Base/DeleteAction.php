<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Base;

use App\Actions\Storage\Base\DeleteAction as BaseDeleteAction;
use App\Contracts\Actions\Storage\StorageAction as StorageActionContract;
use App\Filament\Actions\Storage\StorageAction;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DeleteAction.
 */
abstract class DeleteAction extends StorageAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
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
     * @param  Model|null  $model
     * @param  array  $fields
     * @return BaseDeleteAction
     */
    abstract protected function storageAction(?Model $model, array $fields): StorageActionContract;
}
