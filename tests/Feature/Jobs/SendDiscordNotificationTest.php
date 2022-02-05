<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs;

use App\Constants\Config\FlagConstants;
use App\Contracts\Events\DiscordMessageEvent;
use App\Jobs\Middleware\RateLimited;
use App\Jobs\SendDiscordNotificationJob;
use App\Notifications\DiscordNotification;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;
use NotificationChannels\Discord\DiscordMessage;
use Tests\TestCase;

/**
 * Class SendDiscordNotificationTest.
 */
class SendDiscordNotificationTest extends TestCase
{
    /**
     * The Send Discord Notification Job shall send a DiscordNotification.
     *
     * @return void
     */
    public function testSendDiscordNotificationJobSendsNotification(): void
    {
        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
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
             *
             * @return string
             */
            public function getDiscordChannel(): string
            {
                return '';
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
     *
     * @return void
     */
    public function testRateLimited(): void
    {
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

        $job = new SendDiscordNotificationJob($event);

        $middleware = collect($job->middleware())->first();

        static::assertInstanceOf(RateLimited::class, $middleware);
    }
}
