<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class DiscordNotification.
 */
class DiscordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The Discord message payload.
     *
     * @var DiscordMessage
     */
    protected DiscordMessage $message;

    /**
     * Create a new notification instance.
     *
     * @param  DiscordMessage  $message
     * @return void
     */
    public function __construct(DiscordMessage $message)
    {
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
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
     * @param  mixed  $notifiable
     * @return DiscordMessage
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
     * @param mixed $notifiable
     * @param string $channel
     * @return mixed
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function shouldSend(mixed $notifiable, string $channel): mixed
    {
        return Config::get('flags.allow_discord_notifications', false);
    }
}
