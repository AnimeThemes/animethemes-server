<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Contracts\Events\CascadesRestoresEvent;

/**
 * Class CascadesRestores.
 */
class CascadesRestores
{
    /**
     * Handle the event.
     *
     * @param  CascadesRestoresEvent  $event
     * @return void
     */
    public function handle(CascadesRestoresEvent $event): void
    {
        $event->cascadeRestores();
    }
}
