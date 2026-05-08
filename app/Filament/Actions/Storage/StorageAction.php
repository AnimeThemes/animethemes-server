<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage;

use App\Contracts\Actions\Storage\StorageAction as BaseStorageAction;
use App\Filament\Actions\BaseAction;
use Illuminate\Database\Eloquent\Model;

abstract class StorageAction extends BaseAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->action(fn (?Model $record, array $data) => $this->handle($record, $data));
    }

    /**
     * Get the underlying storage action.
     *
     * @param  array<string, mixed>  $data
     */
    abstract protected function storageAction(Model $record, array $data): BaseStorageAction;

    /**
     * Run this after the video is uploaded.
     *
     * @param  array<string, mixed>  $data
     */
    protected function afterStorageAction(?Model $record, array $data): void {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(?Model $record, array $data): void
    {
        $action = $this->storageAction($record, $data);

        $storageResults = $action->handle();

        $storageResults->toLog();

        $updated = $action->then($storageResults);

        $record ??= $updated;

        $actionResult = $storageResults->toActionResult();

        if ($actionResult->hasFailed()) {
            $this->failedLog($actionResult->getMessage());

            return;
        }

        $this->afterStorageAction($record, $data);
    }
}
