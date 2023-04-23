<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Constants\FeatureConstants;
use App\Contracts\Events\DiscordMessageEvent;
use App\Jobs\SendDiscordNotificationJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Laravel\Pennant\Feature;

/**
 * Class SendDiscordNotification.
 */
class SendDiscordNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  DiscordMessageEvent  $event
     * @return void
     */
    public function handle(DiscordMessageEvent $event): void
    {
        if (Feature::active(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS)) {
            SendDiscordNotificationJob::dispatch($event);
        }
    }

    /**
     * Determine whether the listener should be queued.
     *
     * @param  DiscordMessageEvent  $event
     * @return bool
     */
    public function shouldQueue(DiscordMessageEvent $event): bool
    {
        return $event->shouldSendDiscordMessage();
    }
}
