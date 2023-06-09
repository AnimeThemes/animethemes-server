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
 * Class UploadResults.
 */
readonly class UploadResults implements StorageResults
{
    /**
     * Create a new action result instance.
     *
     * @param  array<string, string|false>  $uploads
     */
    public function __construct(protected array $uploads = [])
    {
    }

    /**
     * Write results to log.
     *
     * @return void
     */
    public function toLog(): void
    {
        if (empty($this->uploads)) {
            Log::error('No uploads were attempted.');
        }
        foreach ($this->uploads as $fs => $result) {
            $result === false
                ? Log::error("Failed to upload to disk '$fs'")
                : Log::info("Uploaded '$result' to disk '$fs'");
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
        if (empty($this->uploads)) {
            $command->error('No uploads were attempted.');
        }
        foreach ($this->uploads as $fs => $result) {
            $result === false
                ? $command->error("Failed to upload to disk '$fs'")
                : $command->info("Uploaded '$result' to disk '$fs'");
        }
    }

    /**
     * Transform to Action Result.
     *
     * @return ActionResult
     */
    public function toActionResult(): ActionResult
    {
        if (empty($this->uploads)) {
            return new ActionResult(
                ActionStatus::FAILED,
                'No uploads were attempted. Please check that disks are configured.'
            );
        }

        /** @var Collection $failed */
        /** @var Collection $passed */
        [$failed, $passed] = collect($this->uploads)->partition(fn (string|false $result, string $fs) => $result === false);

        if ($failed->isNotEmpty()) {
            return new ActionResult(
                ActionStatus::FAILED,
                "Failed to upload to disks {$failed->keys()->join(', ', ' & ')}."
            );
        }

        return new ActionResult(
            ActionStatus::PASSED,
            "Uploaded '{$passed->values()->first()}' to disks {$passed->keys()->join(', ', ' & ')}."
        );
    }
}
