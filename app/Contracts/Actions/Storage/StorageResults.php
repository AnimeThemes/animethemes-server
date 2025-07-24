<?php

declare(strict_types=1);

namespace App\Contracts\Actions\Storage;

use App\Actions\ActionResult;
use Illuminate\Console\Command;

interface StorageResults
{
    /**
     * Write results to log.
     */
    public function toLog(): void;

    /**
     * Write results to console output.
     */
    public function toConsole(Command $command): void;

    /**
     * Transform to Action Result.
     */
    public function toActionResult(): ActionResult;
}
