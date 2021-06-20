<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Contracts\Events\DiscordMessageEvent;
use App\Jobs\SendDiscordNotificationJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Config;

/**
 * Class SendDiscordNotification.
 */
class SendDiscordNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param DiscordMessageEvent $event
     * @return void
     */
    public function handle(DiscordMessageEvent $event)
    {
        if (Config::get('app.allow_discord_notifications', false)) {
            SendDiscordNotificationJob::dispatch($event);
        }
    }
}
