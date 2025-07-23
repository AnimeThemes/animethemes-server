<?php

declare(strict_types=1);

namespace App\Contracts\Events;

interface RemoveFromStorageEvent
{
    /**
     * Remove the image from the bucket.
     */
    public function removeFromStorage(): void;
}
