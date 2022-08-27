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
 * Class MoveResults.
 */
class MoveResults extends StorageResults
{
    /**
     * Create a new action result instance.
     *
     * @param  BaseModel  $model
     * @param  string  $from
     * @param  string  $to
     * @param  array<string, bool>  $moves
     */
    public function __construct(
        protected readonly BaseModel $model,
        protected readonly string $from,
        protected readonly string $to,
        protected readonly array $moves = []
    ) {
    }

    /**
     * Write results to log.
     *
     * @return void
     */
    public function toLog(): void
    {
        if (empty($this->moves)) {
            Log::error('No moves were attempted.');
        }
        foreach ($this->moves as $fs => $result) {
            $result
                ? Log::info("Moved '{$this->model->getName()}' from '$this->from' to '$this->to' in disk '$fs'")
                : Log::error("Failed to move '{$this->model->getName()}' from '$this->from' to '$this->to' in disk '$fs'");
        }
    }

    /**
     * Transform to Action Result.
     *
     * @return ActionResult
     */
    public function toActionResult(): ActionResult
    {
        if (empty($this->moves)) {
            return new ActionResult(
                ActionStatus::FAILED(),
                'No moves were attempted. Please check that disks are configured.'
            );
        }

        /** @var Collection $passed */
        /** @var Collection $failed */
        [$passed, $failed] = collect($this->moves)->partition(fn (bool $result, string $fs) => $result);

        if ($failed->isNotEmpty()) {
            return new ActionResult(
                ActionStatus::FAILED(),
                "Failed to move '{$this->model->getName()}' from '$this->from' to '$this->to' in disks {$failed->keys()->join(', ', ' & ')}."
            );
        }

        return new ActionResult(
            ActionStatus::PASSED(),
            "Moved '{$this->model->getName()}' from '$this->from' to '$this->to' in disks {$passed->keys()->join(', ', ' & ')}."
        );
    }
}
