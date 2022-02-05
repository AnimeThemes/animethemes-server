<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Contracts\Events\CascadesDeletesEvent;

/**
 * Class CascadesDeletes.
 */
class CascadesDeletes
{
    /**
     * Handle the event.
     *
     * @param  CascadesDeletesEvent  $event
     * @return void
     */
    public function handle(CascadesDeletesEvent $event): void
    {
        $event->cascadeDeletes();
    }
}
