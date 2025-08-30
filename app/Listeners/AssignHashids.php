<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Actions\Models\AssignHashidsAction;
use App\Contracts\Events\AssignHashidsEvent;

class AssignHashids
{
    public function handle(AssignHashidsEvent $event): void
    {
        $action = new AssignHashidsAction();

        $action->assign($event->getModel(), $event->getHashidsConnection());
    }
}
