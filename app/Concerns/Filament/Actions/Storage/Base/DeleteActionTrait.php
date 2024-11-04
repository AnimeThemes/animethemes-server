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
     * Get the underlying storage action.
     *
     * @param  BaseModel  $model
     * @param  array  $fields
     * @return BaseDeleteAction
     */
    abstract protected function storageAction(BaseModel $model, array $fields): StorageAction;
}
