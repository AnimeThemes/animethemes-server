<?php

namespace App\Listeners;

use App\Contracts\Events\CascadesRestoresEvent;

class CascadesRestores
{
    /**
     * Handle the event.
     *
     * @param  \App\Contracts\Events\CascadesRestoresEvent  $event
     * @return void
     */
    public function handle(CascadesRestoresEvent $event)
    {
        $event->cascadeRestores();
    }
}
