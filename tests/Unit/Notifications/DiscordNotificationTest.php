<?php

declare(strict_types=1);

namespace Notifications;

use App\Notifications\DiscordNotification;
use Illuminate\Notifications\AnonymousNotifiable;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;
use Tests\TestCase;

/**
 * Class DiscordNotificationTest
 * @package Notifications
 */
class DiscordNotificationTest extends TestCase
{
    /**
     * A Discord Notification shall be delivered to a Discord Channel.
     *
     * @return void
     */
    public function testViaDiscordMessage()
    {
        $message = DiscordMessage::create();

        $notification = new DiscordNotification($message);

        static::assertEquals([DiscordChannel::class], $notification->via(new AnonymousNotifiable()));
    }

    /**
     * A Discord Notification shall deliver a Discord Message.
     *
     * @return void
     */
    public function testToDiscordMessage()
    {
        $message = DiscordMessage::create();

        $notification = new DiscordNotification($message);

        static::assertInstanceOf(DiscordMessage::class, $notification->toDiscord(new AnonymousNotifiable()));
    }
}
