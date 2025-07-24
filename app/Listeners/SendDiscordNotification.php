<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Constants\FeatureConstants;
use App\Contracts\Events\DiscordMessageEvent;
use App\Jobs\SendDiscordNotificationJob;
use Laravel\Pennant\Feature;

class SendDiscordNotification
{
    /**
     * Handle the event.
     */
    public function handle(DiscordMessageEvent $event): void
    {
        if (Feature::for(null)->active(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS) && $event->shouldSendDiscordMessage()) {
            SendDiscordNotificationJob::dispatch($event)
                ->onQueue('discord')
                ->afterCommit();
        }
    }
}
