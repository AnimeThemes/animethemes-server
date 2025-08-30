<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Contracts\Events\FilamentNotificationEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifiesFilamentUsers implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(FilamentNotificationEvent $event): void
    {
        if ($event->shouldSendFilamentNotification()) {
            $event->getFilamentNotification()->sendToDatabase($event->getFilamentNotificationRecipients());
        }
    }
}
