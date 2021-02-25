<?php

namespace Tests\Unit\Jobs;

use App\Contracts\Events\DiscordMessageEvent;
use App\Jobs\Middleware\RateLimited;
use App\Jobs\SendDiscordNotification;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use NotificationChannels\Discord\DiscordMessage;
use Tests\TestCase;

class SendDiscordNotificationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testRateLimited()
    {
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

        $job = new SendDiscordNotification($event);

        $middleware = collect($job->middleware())->first();

        $this->assertInstanceOf(RateLimited::class, $middleware);
    }
}
