<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs;

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
use Tests\TestCase;

class SendDiscordNotificationTest extends TestCase
{
    /**
     * The Send Discord Notification Job shall send a DiscordNotification.
     */
    public function testSendDiscordNotificationJobSendsNotification(): void
    {
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
    }

    /**
     * The Send Discord Notification Job shall use the RateLimited middleware.
     */
    public function testRateLimited(): void
    {
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

        static::assertInstanceOf(RateLimited::class, $middleware);
    }
}
