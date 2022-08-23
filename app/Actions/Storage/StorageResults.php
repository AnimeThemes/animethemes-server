<?php

declare(strict_types=1);

namespace App\Actions\Storage;

use App\Actions\ActionResult;

/**
 * Class StorageResults.
 */
abstract class StorageResults
{
    /**
     * Write results to log.
     *
     * @return void
     */
    abstract public function toLog(): void;

    /**
     * Transform to Action Result.
     *
     * @return ActionResult
     */
    abstract public function toActionResult(): ActionResult;
}
