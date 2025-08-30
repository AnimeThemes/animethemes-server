<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Contracts\Events\NotifiesUsersEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifiesUsers implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(NotifiesUsersEvent $event): void
    {
        $event->notify();
    }
}
