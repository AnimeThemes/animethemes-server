<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Constants\Config\FlagConstants;
use App\Contracts\Events\DiscordMessageEvent;
use App\Jobs\SendDiscordNotificationJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
    public function handle(DiscordMessageEvent $event)
    {
        if (config(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, false)) {
            SendDiscordNotificationJob::dispatch($event);
        }
    }
}
