<?php

declare(strict_types=1);

namespace App\Actions\Storage\Base;

use App\Actions\ActionResult;
use App\Contracts\Actions\Storage\StorageResults;
use App\Enums\Actions\ActionStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Class PruneResults.
 */
class PruneResults implements StorageResults
{
    /**
     * Create a new action result instance.
     *
     * @param  string  $fs
     * @param  array<string, bool>  $prunings
     */
    public function __construct(protected readonly string $fs, protected readonly array $prunings = [])
    {
    }

    /**
     * Write results to log.
     *
     * @return void
     */
    public function toLog(): void
    {
        if (empty($this->prunings)) {
            Log::error('No prunings were attempted.');
        }
        foreach ($this->prunings as $path => $result) {
            $result === false
                ? Log::error("Failed to prune '$path' from disk '$this->fs'")
                : Log::info("Pruned '$path' from disk '$this->fs'");
        }
    }

    /**
     * Write results to console output.
     *
     * @param  Command  $command
     * @return void
     */
    public function toConsole(Command $command): void
    {
        if (empty($this->prunings)) {
            $command->error('No prunings were attempted.');
        }
        foreach ($this->prunings as $path => $result) {
            $result === false
                ? $command->error("Failed to prune '$path' from disk '$this->fs'")
                : $command->info("Pruned '$path' from disk '$this->fs'");
        }
    }

    /**
     * Transform to Action Result.
     *
     * @return ActionResult
     */
    public function toActionResult(): ActionResult
    {
        if (empty($this->prunings)) {
            return new ActionResult(
                ActionStatus::FAILED(),
                'No prunings were attempted.'
            );
        }

        /** @var Collection $passed */
        /** @var Collection $failed */
        [$passed, $failed] = collect($this->prunings)->partition(fn (bool $result, string $path) => $result);

        if ($failed->isNotEmpty()) {
            return new ActionResult(
                ActionStatus::FAILED(),
                "Failed to prune {$failed->keys()->join(', ', ' & ')} from disk '$this->fs'."
            );
        }

        return new ActionResult(
            ActionStatus::PASSED(),
            "Pruned {$passed->keys()->join(', ', ' & ')} from disk '$this->fs'."
        );
    }
}
