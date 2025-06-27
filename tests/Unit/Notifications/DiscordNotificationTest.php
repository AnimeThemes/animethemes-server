<?php

declare(strict_types=1);

namespace Tests\Unit\Notifications;

use App\Notifications\DiscordNotification;
use Illuminate\Notifications\AnonymousNotifiable;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;
use Tests\TestCase;

/**
 * Class DiscordNotificationTest.
 */
class DiscordNotificationTest extends TestCase
{
    /**
     * A Discord Notification shall be delivered to a Discord Channel.
     *
     * @return void
     */
    public function test_via_discord_message(): void
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
    public function test_to_discord_message(): void
    {
        $message = DiscordMessage::create();

        $notification = new DiscordNotification($message);

        static::assertInstanceOf(DiscordMessage::class, $notification->toDiscord(new AnonymousNotifiable()));
    }
}
