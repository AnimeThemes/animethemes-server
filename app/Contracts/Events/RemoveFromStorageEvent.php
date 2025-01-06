<?php

declare(strict_types=1);

namespace App\Contracts\Events;

/**
 * Interface RemoveFromStorageEvent.
 */
interface RemoveFromStorageEvent
{
    /**
     * Remove the image from the bucket.
     *
     * @return void
     */
    public function removeFromStorage(): void;
}
