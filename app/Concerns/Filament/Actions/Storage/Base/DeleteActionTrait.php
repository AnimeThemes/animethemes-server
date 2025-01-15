<?php

declare(strict_types=1);

namespace App\Concerns\Filament\Actions\Storage\Base;

use App\Actions\Storage\Base\DeleteAction as BaseDeleteAction;
use App\Contracts\Actions\Storage\StorageAction;
use App\Models\BaseModel;

/**
 * Trait DeleteActionTrait.
 */
trait DeleteActionTrait
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
     * @param  BaseModel  $model
     * @param  array  $fields
     * @return BaseDeleteAction
     */
    abstract protected function storageAction(BaseModel $model, array $fields): StorageAction;
}
