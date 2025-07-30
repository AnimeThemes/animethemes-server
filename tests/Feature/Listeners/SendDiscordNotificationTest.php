<?php

declare(strict_types=1);

use App\Constants\FeatureConstants;
use App\Contracts\Events\DiscordMessageEvent;
use App\Jobs\SendDiscordNotificationJob;
use App\Listeners\SendDiscordNotification;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Bus;
use Laravel\Pennant\Feature;
use NotificationChannels\Discord\DiscordMessage;

test('discord notifications not allowed', function () {
    Feature::deactivate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);

    $event = new class implements DiscordMessageEvent
    {
        use Dispatchable;

        /**
         * Get Discord message payload.
         */
        public function getDiscordMessage(): DiscordMessage
        {
            return DiscordMessage::create();
        }

        /**
         * Get Discord channel the message will be sent to.
         */
        public function getDiscordChannel(): string
        {
            return '';
        }

        /**
         * Determine if the message should be sent.
         */
        public function shouldSendDiscordMessage(): bool
        {
            return true;
        }
    };
    $listener = new SendDiscordNotification();

    $listener->handle($event);

    Bus::assertNotDispatched(SendDiscordNotificationJob::class);
});

test('discord notifications allowed', function () {
    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);

    $event = new class implements DiscordMessageEvent
    {
        use Dispatchable;

        /**
         * Get Discord message payload.
         */
        public function getDiscordMessage(): DiscordMessage
        {
            return DiscordMessage::create();
        }

        /**
         * Get Discord channel the message will be sent to.
         */
        public function getDiscordChannel(): string
        {
            return '';
        }

        /**
         * Determine if the message should be sent.
         */
        public function shouldSendDiscordMessage(): bool
        {
            return true;
        }
    };
    $listener = new SendDiscordNotification();

    $listener->handle($event);

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});
