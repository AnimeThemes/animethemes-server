<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Contracts\Events\ManagesTrackEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Class ManagesTrack.
 */
class ManagesTrack implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  ManagesTrackEvent  $event
     * @return void
     */
    public function handle(ManagesTrackEvent $event): void
    {
        $event->manageTrack();
    }
}
