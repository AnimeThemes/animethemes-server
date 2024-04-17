<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Storage\Base;

use App\Actions\Storage\Base\DeleteAction as BaseDeleteAction;
use App\Filament\HeaderActions\Storage\StorageHeaderAction;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DeleteHeaderAction.
 */
abstract class DeleteHeaderAction extends StorageHeaderAction
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
