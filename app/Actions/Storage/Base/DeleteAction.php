<?php

declare(strict_types=1);

namespace App\Actions\Storage\Base;

use App\Contracts\Actions\Storage\StorageAction;
use App\Contracts\Actions\Storage\StorageResults;
use App\Contracts\Storage\InteractsWithDisks;
use App\Models\BaseModel;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

/**
 * Class DeleteAction.
 *
 * @template TModel of \App\Models\BaseModel
 */
abstract class DeleteAction implements InteractsWithDisks, StorageAction
{
    /**
     * Create a new action instance.
     *
     * @param  TModel  $model
     */
    public function __construct(protected BaseModel $model)
    {
    }

    /**
     * Handle action.
     *
     * @return StorageResults
     */
    public function handle(): StorageResults
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
     * Processes to be completed after handling action.
     *
     * @param  StorageResults  $storageResults
     * @return TModel
     */
    public function then(StorageResults $storageResults): BaseModel
    {
        $this->model->delete();

        return $this->model;
    }

    /**
     * Get the path to delete.
     *
     * @return string
     */
    abstract protected function path(): string;
}
