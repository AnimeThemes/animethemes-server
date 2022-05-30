<?php

declare(strict_types=1);

namespace Tests\Feature\Listeners;

use App\Constants\Config\FlagConstants;
use App\Contracts\Events\DiscordMessageEvent;
use App\Jobs\SendDiscordNotificationJob;
use App\Listeners\SendDiscordNotification;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;
use Tests\TestCase;

/**
 * Class SendDiscordNotificationTest.
 */
class SendDiscordNotificationTest extends TestCase
{
    /**
     * If discord notifications are disabled through the 'flags.allow_discord_notifications' property,
     * discord notification jobs shall not be dispatched.
     *
     * @return void
     */
    public function testDiscordNotificationsNotAllowed(): void
    {
        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, false);
        Bus::fake(SendDiscordNotificationJob::class);

        $event = new class implements DiscordMessageEvent
        {
            use Dispatchable;

            /**
             * Get Discord message payload.
             *
             * @return DiscordMessage
             */
            public function getDiscordMessage(): DiscordMessage
            {
                return DiscordMessage::create();
            }

            /**
             * Get Discord channel the message will be sent to.
             *
             * @return string
             */
            public function getDiscordChannel(): string
            {
                return '';
            }
        };

        $listener = new SendDiscordNotification();

        $listener->handle($event);

        Bus::assertNotDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * If discord notifications are enabled through the 'flags.allow_discord_notifications' property,
     * discord notification jobs shall be dispatched.
     *
     * @return void
     */
    public function testDiscordNotificationsAllowed(): void
    {
        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $event = new class implements DiscordMessageEvent
        {
            use Dispatchable;

            /**
             * Get Discord message payload.
             *
             * @return DiscordMessage
             */
            public function getDiscordMessage(): DiscordMessage
            {
                return DiscordMessage::create();
            }

            /**
             * Get Discord channel the message will be sent to.
             *
             * @return string
             */
            public function getDiscordChannel(): string
            {
                return '';
            }
        };

        $listener = new SendDiscordNotification();

        $listener->handle($event);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
