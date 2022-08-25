<?php

declare(strict_types=1);

namespace App\Actions\Storage\Base;

use App\Actions\Storage\StorageAction;
use App\Actions\Storage\StorageResults;
use App\Models\BaseModel;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

/**
 * Class MoveAction.
 *
 * @template TModel of \App\Models\BaseModel
 */
abstract class MoveAction extends StorageAction
{
    /**
     * Create a new action instance.
     *
     * @param  TModel  $model
     */
    public function __construct(protected BaseModel $model, protected readonly string $to)
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

        $from = $this->from();

        foreach ($this->disks() as $disk) {
            /** @var FilesystemAdapter $fs */
            $fs = Storage::disk($disk);

            $result = $fs->move($from, $this->to);

            $results[$disk] = $result;
        }

        $this->update();

        return new MoveResults($this->model, $from, $this->to, $results);
    }

    /**
     * Get the path to move from.
     *
     * @return string
     */
    abstract protected function from(): string;

    /**
     * Update underlying model.
     * We want to apply these updates through Eloquent to preserve relations when renaming.
     * Otherwise, reconciliation would destroy the old model and create a new model for the new name.
     *
     * @return void
     */
    abstract protected function update(): void;
}
