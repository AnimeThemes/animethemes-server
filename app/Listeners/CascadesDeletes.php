<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Contracts\Events\CascadesDeletesEvent;

class CascadesDeletes
{
    /**
     * Handle the event.
     */
    public function handle(CascadesDeletesEvent $event): void
    {
        $event->cascadeDeletes();
    }
}
