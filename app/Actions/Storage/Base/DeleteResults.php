<?php

declare(strict_types=1);

namespace App\Actions\Storage\Base;

use App\Actions\ActionResult;
use App\Actions\Storage\StorageResults;
use App\Enums\Actions\ActionStatus;
use App\Models\BaseModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Class DeleteResults.
 */
class DeleteResults extends StorageResults
{
    /**
     * Create a new action result instance.
     *
     * @param  BaseModel  $model
     * @param  array<string, bool>  $deletions
     */
    public function __construct(protected readonly BaseModel $model, protected readonly array $deletions = [])
    {
    }

    /**
     * Write results to log.
     *
     * @return void
     */
    public function toLog(): void
    {
        if (empty($this->deletions)) {
            Log::error('No deletions were attempted.');
        }
        foreach ($this->deletions as $fs => $result) {
            $result
                ? Log::info("Deleted '{$this->model->getName()}' from disk '$fs'")
                : Log::error("Failed to delete '{$this->model->getName()}' from disk '$fs'");
        }
    }

    /**
     * Transform to Action Result.
     *
     * @return ActionResult
     */
    public function toActionResult(): ActionResult
    {
        if (empty($this->deletions)) {
            return new ActionResult(
                ActionStatus::FAILED(),
                'No deletions were attempted. Please check that disks are configured.'
            );
        }

        /** @var Collection $passed */
        /** @var Collection $failed */
        [$passed, $failed] = collect($this->deletions)->partition(fn (bool $result, string $fs) => $result);

        if ($failed->isNotEmpty()) {
            return new ActionResult(
                ActionStatus::FAILED(),
                "Failed to delete '{$this->model->getName()}' from disks {$failed->keys()->join(', ', ' & ')}."
            );
        }

        return new ActionResult(
            ActionStatus::PASSED(),
            "Deleted '{$this->model->getName()}' from disks {$passed->keys()->join(', ', ' & ')}."
        );
    }
}