<?php

declare(strict_types=1);

namespace App\Contracts\Actions\Storage;

/**
 * Interface StorageAction.
 */
interface StorageAction
{
    /**
     * Handle action.
     *
     * @return StorageResults
     */
    public function handle(): StorageResults;
}
