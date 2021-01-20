<?php

namespace App\Listeners;

use App\Events\DiscordMessageEvent;
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
     * @param  \App\Events\DiscordMessageEvent  $event
     * @return void
     */
    public function handle(DiscordMessageEvent $event)
    {
        Notification::route('discord', Config::get('services.discord.channel_id'))
            ->notify(new DiscordNotification($event->getDiscordMessage()));
    }
}
