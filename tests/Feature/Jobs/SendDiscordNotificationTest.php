<?php declare(strict_types=1);

namespace Jobs;

use App\Contracts\Events\DiscordMessageEvent;
use App\Jobs\Middleware\RateLimited;
use App\Jobs\SendDiscordNotification;
use App\Notifications\DiscordNotification;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use NotificationChannels\Discord\DiscordMessage;
use Tests\TestCase;

/**
 * Class SendDiscordNotificationTest
 * @package Jobs
 */
class SendDiscordNotificationTest extends TestCase
{
    /**
     * The Send Discord Notification Job shall send a DiscordNotification.
     *
     * @return void
     */
    public function testSendDiscordNotificationJobSendsNotification()
    {
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

        $job = new SendDiscordNotification($event);

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
    public function testRateLimited()
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

        $job = new SendDiscordNotification($event);

        $middleware = collect($job->middleware())->first();

        static::assertInstanceOf(RateLimited::class, $middleware);
    }
}
