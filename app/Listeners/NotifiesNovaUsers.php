<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Contracts\Events\NovaNotificationEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Class NotifiesNovaUsers.
 */
class NotifiesNovaUsers implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  NovaNotificationEvent  $event
     * @return void
     */
    public function handle(NovaNotificationEvent $event): void
    {
        if ($event->shouldSend()) {
            $notification = $event->getNotification();
            foreach ($event->getUsers() as $user) {
                $user->notify($notification);
            }
        }
    }
}
