<?php

declare(strict_types=1);

namespace App\Actions\Storage\Base;

use App\Actions\Storage\StorageAction;
use App\Actions\Storage\StorageResults;
use App\Models\BaseModel;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

/**
 * Class DeleteAction.
 *
 * @template TModel of \App\Models\BaseModel
 */
abstract class DeleteAction extends StorageAction
{
    /**
     * Create a new action instance.
     *
     * @param  TModel  $model
     */
    public function __construct(protected readonly BaseModel $model)
    {
    }

    /**
     * Delete the file from configured disks.
     *
     * @return StorageResults
     */
    protected function handleStorageAction(): StorageResults
    {
        $results = [];

        foreach ($this->disks() as $disk) {
            /** @var FilesystemAdapter $fs */
            $fs = Storage::disk($disk);

            $result = $fs->delete($this->path());

            $results[$disk] = $result;
        }

        return new DeleteResults($this->model, $results);
    }

    /**
     * Get the path to delete.
     *
     * @return string
     */
    abstract protected function path(): string;
}
