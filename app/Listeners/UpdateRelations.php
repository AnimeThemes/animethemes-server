<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Contracts\Events\UpdateRelationsEvent;

class UpdateRelations
{
    public function handle(UpdateRelationsEvent $event): void
    {
        $event->updateRelations();
    }
}
