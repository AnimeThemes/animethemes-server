<?php

namespace App\Listeners;

use App\Contracts\Events\CascadesDeletesEvent;

class CascadesDeletes
{
    /**
     * Handle the event.
     *
     * @param \App\Contracts\Events\CascadesDeletesEvent $event
     * @return void
     */
    public function handle(CascadesDeletesEvent $event)
    {
        $event->cascadeDeletes();
    }
}
