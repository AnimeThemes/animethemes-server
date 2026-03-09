<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Contracts\Events\CreateSynonymEvent;

class CreateSynonym
{
    public function handle(CreateSynonymEvent $event): void
    {
        $event->createSynonym();
    }
}
