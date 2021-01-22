<?php

namespace App\Listeners;

use App\Events\CascadesDeletesEvent;

class CascadesDeletes
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\CascadesDeletesEvent  $event
     * @return void
     */
    public function handle(CascadesDeletesEvent $event)
    {
        $event->cascadeDeletes();
    }
}
