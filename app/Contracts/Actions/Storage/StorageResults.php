<?php

declare(strict_types=1);

namespace App\Contracts\Actions\Storage;

use App\Actions\ActionResult;
use Illuminate\Console\Command;

/**
 * Interface StorageResults.
 */
interface StorageResults
{
    /**
     * Write results to log.
     *
     * @return void
     */
    public function toLog(): void;

    /**
     * Write results to console output.
     *
     * @param  Command  $command
     * @return void
     */
    public function toConsole(Command $command): void;

    /**
     * Transform to Action Result.
     *
     * @return ActionResult
     */
    public function toActionResult(): ActionResult;
}
