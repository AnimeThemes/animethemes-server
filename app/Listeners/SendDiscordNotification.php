<?php

namespace App\Listeners;

use App\Contracts\Events\DiscordMessageEvent;
use App\Notifications\DiscordNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;

class SendDiscordNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  \App\Contracts\Events\DiscordMessageEvent  $event
     * @return void
     */
    public function handle(DiscordMessageEvent $event)
    {
        if (Config::get('app.allow_discord_notifications', false)) {
            Notification::route('discord', $event->getDiscordChannel())
                ->notify(new DiscordNotification($event->getDiscordMessage()));
        }
    }
}
