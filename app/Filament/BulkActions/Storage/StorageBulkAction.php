<?php

declare(strict_types=1);

namespace App\Filament\BulkActions\Storage;

use App\Contracts\Actions\Storage\StorageAction as BaseStorageAction;
use App\Filament\BulkActions\BaseBulkAction;
use App\Models\BaseModel;
use Illuminate\Support\Collection;

abstract class StorageBulkAction extends BaseBulkAction
{
    /**
     * Get the underlying storage action.
     *
     * @param  array<string, mixed>  $data
     */
    abstract protected function storageAction(BaseModel $model, array $data): BaseStorageAction;

    /**
     * @param  Collection<int, BaseModel>  $models
     * @param  array<string, mixed>  $data
     */
    public function handle(Collection $models, array $data): void
    {
        foreach ($models as $model) {
            $action = $this->storageAction($model, $data);

            $storageResults = $action->handle();

            $storageResults->toLog();

            $action->then($storageResults);

            $actionResult = $storageResults->toActionResult();

            if ($actionResult->hasFailed()) {
                $this->failedLog($actionResult->getMessage());
            }
        }
    }
}
