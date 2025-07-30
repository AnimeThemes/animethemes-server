<?php

declare(strict_types=1);

use App\Constants\FeatureConstants;
use App\Contracts\Events\DiscordMessageEvent;
use App\Jobs\Middleware\RateLimited;
use App\Jobs\SendDiscordNotificationJob;
use App\Notifications\DiscordNotification;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Laravel\Pennant\Feature;
use NotificationChannels\Discord\DiscordMessage;

use function Pest\Laravel\get;

test('send discord notification job sends notification', function () {
    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Notification::fake();

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
    $job = new SendDiscordNotificationJob($event);

    $job->handle();

    Notification::assertSentTo(
        new AnonymousNotifiable(),
        DiscordNotification::class,
    );
});

test('rate limited', function () {
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
    $job = new SendDiscordNotificationJob($event);

    $middleware = collect($job->middleware())->first();

    $this->assertInstanceOf(RateLimited::class, $middleware);
});
