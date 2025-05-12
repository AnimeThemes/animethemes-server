<?php

declare(strict_types=1);

namespace App\Filament\BulkActions\Storage\Base;

use App\Actions\Storage\Base\DeleteAction as BaseDeleteAction;
use App\Filament\BulkActions\Storage\StorageBulkAction;
use App\Models\BaseModel;

/**
 * Class DeleteBulkAction.
 */
abstract class DeleteBulkAction extends StorageBulkAction
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
