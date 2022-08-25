<?php

declare(strict_types=1);

namespace App\Actions\Storage;

/**
 * Class StorageAction.
 */
abstract class StorageAction
{
    /**
     * Handle action.
     *
     * @return StorageResults
     */
    abstract public function handle(): StorageResults;

    /**
     * Get the disks to update.
     *
     * @return array
     */
    abstract protected function disks(): array;
}
