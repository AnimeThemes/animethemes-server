<?php

namespace App\Listeners;

use App\Events\BaseEvent;
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
     * @param  \App\Events\BaseEvent  $event
     * @return void
     */
    public function handle(BaseEvent $event)
    {
        Notification::route('discord', Config::get('services.discord.channel_id'))
            ->notify(new DiscordNotification($event->getDiscordMessage()));
    }
}
