<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Storage\Base;

use App\Actions\Storage\Base\DeleteAction as BaseDeleteAction;
use App\Filament\HeaderActions\Storage\StorageHeaderAction;
use App\Models\BaseModel;

/**
 * Class DeleteHeaderAction.
 */
abstract class DeleteHeaderAction extends StorageHeaderAction
{
    /**
     * Get the underlying storage action.
     *
     * @param  BaseModel  $model
     * @param  array  $fields
     * @return BaseDeleteAction
     */
    abstract protected function storageAction(BaseModel $model, array $fields): BaseDeleteAction;
}
