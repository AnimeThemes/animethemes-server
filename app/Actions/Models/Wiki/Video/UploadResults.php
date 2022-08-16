<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki\Video;

use App\Actions\Models\ActionResult;
use App\Enums\Actions\ActionStatus;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Class UploadResults.
 */
class UploadResults
{
    /**
     * Create a new action result instance.
     *
     * @param  array<string, string|false>  $uploads
     */
    public function __construct(protected readonly array $uploads = [])
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
     * Transform to Action Result.
     *
     * @return ActionResult
     */
    public function toActionResult(): ActionResult
    {
        if (empty($this->uploads)) {
            return new ActionResult(
                ActionStatus::FAILED(),
                'No uploads were attempted. Please check that upload disks are configured.'
            );
        }

        /** @var Collection $failed */
        /** @var Collection $passed */
        [$failed, $passed] = collect($this->uploads)->partition(fn (string|false $result, string $fs) => $result === false);

        if ($failed->isNotEmpty()) {
            return new ActionResult(
                ActionStatus::FAILED(),
                "Failed to upload to disks {$failed->keys()->join(', ', ' & ')}."
            );
        }

        return new ActionResult(
            ActionStatus::PASSED(),
            "Uploaded '{$passed->values()->first()}' to disks {$passed->keys()->join(', ', ' & ')}."
        );
    }
}
