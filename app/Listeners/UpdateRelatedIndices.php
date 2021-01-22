<?php

namespace App\Listeners;

use App\Scout\Events\UpdateRelatedIndicesEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateRelatedIndices implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  \App\Scout\Events\UpdateRelatedIndicesEvent  $event
     * @return void
     */
    public function handle(UpdateRelatedIndicesEvent $event)
    {
        $event->updateRelatedIndices();
    }
}
