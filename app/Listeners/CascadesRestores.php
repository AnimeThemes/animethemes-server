<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Contracts\Events\CascadesRestoresEvent;

class CascadesRestores
{
    public function handle(CascadesRestoresEvent $event): void
    {
        $event->cascadeRestores();
    }
}
