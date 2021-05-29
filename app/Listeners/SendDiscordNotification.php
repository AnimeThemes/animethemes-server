<?php

namespace App\Listeners;

use App\Contracts\Events\DiscordMessageEvent;
use App\Jobs\SendDiscordNotification as SendDiscordNotificationJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Config;

class SendDiscordNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param \App\Contracts\Events\DiscordMessageEvent $event
     * @return void
     */
    public function handle(DiscordMessageEvent $event)
    {
        if (Config::get('app.allow_discord_notifications', false)) {
            SendDiscordNotificationJob::dispatch($event);
        }
    }
}
