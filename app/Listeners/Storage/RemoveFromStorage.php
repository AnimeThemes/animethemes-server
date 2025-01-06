<?php

declare(strict_types=1);

namespace App\Listeners\Storage;

use App\Contracts\Events\RemoveFromStorageEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Class RemoveFromStorage.
 */
class RemoveFromStorage implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  RemoveFromStorageEvent  $event
     * @return void
     */
    public function handle(RemoveFromStorageEvent $event): void
    {
        $event->removeFromStorage();
    }
}
