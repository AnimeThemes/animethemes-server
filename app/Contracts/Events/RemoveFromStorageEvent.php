<?php

declare(strict_types=1);

namespace App\Contracts\Events;

interface RemoveFromStorageEvent
{
    public function removeFromStorage(): void;
}
