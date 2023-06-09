<?php

declare(strict_types=1);

namespace App\Actions\Repositories;

use App\Actions\ActionResult;
use App\Enums\Actions\ActionStatus;
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
abstract class ReconcileResults extends ActionResult
{
    /**
     * Create a new results instance.
     *
     * @param  Collection  $created
     * @param  Collection  $deleted
     * @param  Collection  $updated
     */
    public function __construct(
        protected readonly Collection $created = new Collection(),
        protected readonly Collection $deleted = new Collection(),
        protected readonly Collection $updated = new Collection()
    ) {
        parent::__construct(ActionStatus::PASSED);
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
     * Get deleted models.
     *
     * @return Collection
     */
    public function getDeleted(): Collection
    {
        return $this->deleted;
    }

    /**
     * Get updated models.
     *
     * @return Collection
     */
    public function getUpdated(): Collection
    {
        return $this->updated;
    }

    /**
     * Determines if any successful changes were made during reconciliation.
     *
     * @return bool
     */
    public function hasChanges(): bool
    {
        return $this->created->isNotEmpty() || $this->deleted->isNotEmpty() || $this->updated->isNotEmpty();
    }

    /**
     * Write reconcile results to log.
     *
     * @return void
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toLog(): void
    {
        $this->created->each(fn (BaseModel $model) => Log::info("{$this->label()} '{$model->getName()}' created"));
        $this->deleted->each(fn (BaseModel $model) => Log::info("{$this->label()} '{$model->getName()}' deleted"));
        $this->updated->each(fn (BaseModel $model) => Log::info("{$this->label()} '{$model->getName()}' updated"));

        Log::info($this->getMessage());
    }

    /**
     * Write reconcile results to console output.
     *
     * @param  Command  $command
     * @return void
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toConsole(Command $command): void
    {
        $this->created->each(fn (BaseModel $model) => $command->info("{$this->label()} '{$model->getName()}' created"));
        $this->deleted->each(fn (BaseModel $model) => $command->info("{$this->label()} '{$model->getName()}' deleted"));
        $this->updated->each(fn (BaseModel $model) => $command->info("{$this->label()} '{$model->getName()}' updated"));

        $command->info($this->getMessage());
    }

    /**
     * Get the action result message.
     *
     * @return string|null
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getMessage(): ?string
    {
        if ($this->hasChanges()) {
            return "{$this->created->count()} {$this->label($this->created)} created, {$this->deleted->count()} {$this->label($this->deleted)} deleted, {$this->updated->count()} {$this->label($this->updated)} updated";
        }

        return "No {$this->label($this->created)} created or deleted or updated";
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
        return Str::plural(Str::headline(class_basename($this->model())), $models);
    }
}
