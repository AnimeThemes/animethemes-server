<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Base;

use App\Actions\Storage\Base\DeleteAction as BaseDeleteAction;
use App\Filament\Actions\Storage\StorageAction;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DeleteAction.
 */
abstract class DeleteAction extends StorageAction
{
    /**
     * Get the underlying storage action.
     *
     * @param  Model  $model
     * @param  array  $fields
     * @return BaseDeleteAction
     */
    abstract protected function storageAction(Model $model, array $fields): BaseDeleteAction;
}
