<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Contracts\Events\CascadesRestoresEvent;

/**
 * Class CascadesRestores
 * @package App\Listeners
 */
class CascadesRestores
{
    /**
     * Handle the event.
     *
     * @param CascadesRestoresEvent $event
     * @return void
     */
    public function handle(CascadesRestoresEvent $event)
    {
        $event->cascadeRestores();
    }
}
