<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;

class DiscordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The Discord message payload.
     *
     * @var \NotificationChannels\Discord\DiscordMessage
     */
    protected $message;

    /**
     * Create a new notification instance.
     *
     * @param \NotificationChannels\Discord\DiscordMessage $message
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
     */
    public function via($notifiable)
    {
        return [DiscordChannel::class];
    }

    /**
     * Get the discord representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function toDiscord($notifiable)
    {
        return $this->message;
    }
}
