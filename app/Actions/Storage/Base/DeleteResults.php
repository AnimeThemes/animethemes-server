<?php

declare(strict_types=1);

namespace App\Actions\Storage\Base;

use App\Actions\ActionResult;
use App\Contracts\Actions\Storage\StorageResults;
use App\Enums\Actions\ActionStatus;
use App\Models\BaseModel;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

readonly class DeleteResults implements StorageResults
{
    /**
     * @param  array<string, bool>  $deletions
     */
    public function __construct(protected BaseModel $model, protected array $deletions = []) {}

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

    public function toConsole(Command $command): void
    {
        if (empty($this->deletions)) {
            $command->error('No deletions were attempted.');
        }
        foreach ($this->deletions as $fs => $result) {
            $result
                ? $command->info("Deleted '{$this->model->getName()}' from disk '$fs'")
                : $command->error("Failed to delete '{$this->model->getName()}' from disk '$fs'");
        }
    }

    public function toActionResult(): ActionResult
    {
        if (empty($this->deletions)) {
            return new ActionResult(
                ActionStatus::FAILED,
                'No deletions were attempted. Please check that disks are configured.'
            );
        }

        /** @var Collection $passed */
        /** @var Collection $failed */
        [$passed, $failed] = collect($this->deletions)->partition(fn (bool $result, string $fs) => $result);

        if ($failed->isNotEmpty()) {
            return new ActionResult(
                ActionStatus::FAILED,
                "Failed to delete '{$this->model->getName()}' from disks {$failed->keys()->join(', ', ' & ')}."
            );
        }

        return new ActionResult(
            ActionStatus::PASSED,
            "Deleted '{$this->model->getName()}' from disks {$passed->keys()->join(', ', ' & ')}."
        );
    }
}
