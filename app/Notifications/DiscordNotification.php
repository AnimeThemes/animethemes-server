<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Constants\FeatureConstants;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Laravel\Pennant\Feature;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;

class DiscordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected readonly DiscordMessage $message) {}

    /**
     * Get the notification's delivery channels.
     *
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function via(mixed $notifiable): array
    {
        return [DiscordChannel::class];
    }

    /**
     * Get the discord representation of the notification.
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function toDiscord(mixed $notifiable): DiscordMessage
    {
        return $this->message;
    }

    /**
     * Determines if the notification can be sent.
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function shouldSend(mixed $notifiable, string $channel): bool
    {
        return Feature::for(null)->active(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    }
}
