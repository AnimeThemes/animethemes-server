<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Constants\FeatureConstants;
use App\Contracts\Events\DiscordMessageEvent;
use App\Jobs\SendDiscordNotificationJob;
use Laravel\Pennant\Feature;

/**
 * Class SendDiscordNotification.
 */
class SendDiscordNotification
{
    /**
     * Handle the event.
     *
     * @param  DiscordMessageEvent  $event
     * @return void
     */
    public function handle(DiscordMessageEvent $event): void
    {
        if (Feature::active(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS) && $event->shouldSendDiscordMessage()) {
            SendDiscordNotificationJob::dispatch($event)
                ->onQueue('discord')
                ->afterCommit();
        }
    }
}
