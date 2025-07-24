<?php

declare(strict_types=1);

namespace App\Contracts\Actions\Storage;

interface StorageAction
{
    /**
     * Handle action.
     */
    public function handle(): StorageResults;

    /**
     * Processes to be completed after handling action.
     */
    public function then(StorageResults $storageResults): mixed;
}
