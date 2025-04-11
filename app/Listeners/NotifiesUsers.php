<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Contracts\Events\NotifiesUsersEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Class NotifiesUsers.
 */
class NotifiesUsers implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  NotifiesUsersEvent  $event
     * @return void
     */
    public function handle(NotifiesUsersEvent $event): void
    {
        $event->notify();
    }
}
