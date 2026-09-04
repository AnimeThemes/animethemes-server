<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Constants\FeatureConstants;
use App\Contracts\Events\DiscordMessageEvent;
use App\Jobs\Middleware\RateLimited;
use App\Notifications\DiscordNotification;
use DateTime;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Notification;
use Laravel\Pennant\Feature;

#[Backoff(60)]
class SendDiscordNotificationJob implements ShouldQueue
{
    use Queueable;

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
        return Date::now()->addMinutes(15);
    }
}
