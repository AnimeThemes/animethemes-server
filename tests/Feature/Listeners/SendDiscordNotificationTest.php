<?php

namespace Tests\Feature\Listeners;

use App\Contracts\Events\DiscordMessageEvent;
use App\Jobs\SendDiscordNotification as SendDiscordNotificationJob;
use App\Listeners\SendDiscordNotification;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;
use Tests\TestCase;

class SendDiscordNotificationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * If discord notifications are disabled through the 'app.allow_discord_notifications' property,
     * discord notification jobs shall not be dispatched.
     *
     * @return void
     */
    public function testDiscordNotificationsNotAllowed()
    {
        Config::set('app.allow_discord_notifications', false);
        Bus::fake(SendDiscordNotificationJob::class);

        $event = new class implements DiscordMessageEvent {
            use Dispatchable;

            /**
             * Get Discord message payload.
             *
             * @return \NotificationChannels\Discord\DiscordMessage
             */
            public function getDiscordMessage()
            {
                return DiscordMessage::create();
            }

            /**
             * Get Discord channel the message will be sent to.
             *
             * @return string
             */
            public function getDiscordChannel()
            {
                return '';
            }
        };

        $listener = new SendDiscordNotification();

        $listener->handle($event);

        Bus::assertNotDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * If discord notifications are enabled through the 'app.allow_discord_notifications' property,
     * discord notification jobs shall be dispatched.
     *
     * @return void
     */
    public function testDiscordNotificationsAllowed()
    {
        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $event = new class implements DiscordMessageEvent {
            use Dispatchable;

            /**
             * Get Discord message payload.
             *
             * @return \NotificationChannels\Discord\DiscordMessage
             */
            public function getDiscordMessage()
            {
                return DiscordMessage::create();
            }

            /**
             * Get Discord channel the message will be sent to.
             *
             * @return string
             */
            public function getDiscordChannel()
            {
                return '';
            }
        };

        $listener = new SendDiscordNotification();

        $listener->handle($event);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
