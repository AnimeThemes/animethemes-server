<?php

declare(strict_types=1);

namespace Tests\Feature\Listeners;

use App\Constants\FeatureConstants;
use App\Contracts\Events\DiscordMessageEvent;
use App\Jobs\SendDiscordNotificationJob;
use App\Listeners\SendDiscordNotification;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Bus;
use Laravel\Pennant\Feature;
use NotificationChannels\Discord\DiscordMessage;
use Tests\TestCase;

class SendDiscordNotificationTest extends TestCase
{
    /**
     * If discord notifications are disabled through the Allow Discord Notifications feature,
     * discord notification jobs shall not be dispatched.
     */
    public function testDiscordNotificationsNotAllowed(): void
    {
        Feature::deactivate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
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

            /**
             * Determine if the message should be sent.
             *
             * @return bool
             */
            public function shouldSendDiscordMessage(): bool
            {
                return true;
            }
        };

        $listener = new SendDiscordNotification();

        $listener->handle($event);

        Bus::assertNotDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * If discord notifications are enabled through the Allow Discord Notifications feature,
     * discord notification jobs shall be dispatched.
     */
    public function testDiscordNotificationsAllowed(): void
    {
        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
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

            /**
             * Determine if the message should be sent.
             *
             * @return bool
             */
            public function shouldSendDiscordMessage(): bool
            {
                return true;
            }
        };

        $listener = new SendDiscordNotification();

        $listener->handle($event);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
