<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Constants\FeatureConstants;
use App\Contracts\Events\DiscordMessageEvent;
use App\Jobs\Middleware\RateLimited;
use App\Notifications\DiscordNotification;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;
use Laravel\Pennant\Feature;

class SendDiscordNotificationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(protected readonly DiscordMessageEvent $event) {}

    public function handle(): void
    {
        if (Feature::for(null)->active(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS)) {
            Notification::route('discord', $this->event->getDiscordChannel())
                ->notify(new DiscordNotification($this->event->getDiscordMessage()));
        }
    }

    public function middleware(): array
    {
        return [new RateLimited()];
    }

    /**
     * Determine the time at which the job should time out.
     */
    public function retryUntil(): DateTime
    {
        return now()->addMinutes(15);
    }
}
