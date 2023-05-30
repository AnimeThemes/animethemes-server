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

    /**
     * Processes to be completed after handling action.
     *
     * @param  StorageResults  $storageResults
     * @return mixed
     */
    public function then(StorageResults $storageResults): mixed;
}
