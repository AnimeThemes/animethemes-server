<?php

declare(strict_types=1);

namespace App\Actions\Repositories;

use App\Models\BaseModel;
use Countable;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class ReconcileResults.
 *
 * @template TModel of \App\Models\BaseModel
 */
abstract class ReconcileResults
{
    /**
     * Create a new results instance.
     *
     * @param  Collection  $created
     * @param  Collection  $deleted
     * @param  Collection  $updated
     */
    public function __construct(
        protected readonly Collection $created,
        protected readonly Collection $deleted,
        protected readonly Collection $updated
    ) {
    }

    /**
     * Get created models.
     *
     * @return Collection
     */
    public function getCreated(): Collection
    {
        return $this->created;
    }

    /**
     * Determines if any successful changes were made during reconciliation.
     *
     * @return bool
     */
    protected function hasChanges(): bool
    {
        return $this->created->isNotEmpty() || $this->deleted->isNotEmpty() || $this->updated->isNotEmpty();
    }

    /**
     * Write reconcile results to log.
     *
     * @return void
     */
    public function toLog(): void
    {
        $this->created->each(fn (BaseModel $model) => Log::info("{$this->label()} '{$model->getName()}' created"));
        $this->deleted->each(fn (BaseModel $model) => Log::info("{$this->label()} '{$model->getName()}' deleted"));
        $this->updated->each(fn (BaseModel $model) => Log::info("{$this->label()} '{$model->getName()}' updated"));

        if ($this->hasChanges()) {
            Log::info("{$this->created->count()} {$this->label($this->created)} created, {$this->deleted->count()} {$this->label($this->deleted)} deleted, {$this->updated->count()} {$this->label($this->updated)} updated");
        } else {
            Log::info("No {$this->label($this->created)} created or deleted or updated");
        }
    }

    /**
     * Write reconcile results to console output.
     *
     * @param  Command  $command
     * @return void
     */
    public function toConsole(Command $command): void
    {
        $this->created->each(fn (BaseModel $model) => $command->info("{$this->label()} '{$model->getName()}' created"));
        $this->deleted->each(fn (BaseModel $model) => $command->info("{$this->label()} '{$model->getName()}' deleted"));
        $this->updated->each(fn (BaseModel $model) => $command->info("{$this->label()} '{$model->getName()}' updated"));

        if ($this->hasChanges()) {
            $command->info("{$this->created->count()} {$this->label($this->created)} created, {$this->deleted->count()} {$this->label($this->deleted)} deleted, {$this->updated->count()} {$this->label($this->updated)} updated");
        } else {
            $command->info("No {$this->label($this->created)} created or deleted or updated");
        }
    }

    /**
     * Get the model of the reconciliation results.
     *
     * @return class-string<TModel>
     */
    abstract protected function model(): string;

    /**
     * Get the user-friendly label for the model class name.
     *
     * @param  int|array|Countable  $models
     * @return string
     */
    protected function label(int|array|Countable $models = 1): string
    {
        return Str::plural(class_basename($this->model()), $models);
    }
}
