<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Contracts\Events\ManagesTrackEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ManagesTrack implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(ManagesTrackEvent $event): void
    {
        $event->manageTrack();
    }
}
