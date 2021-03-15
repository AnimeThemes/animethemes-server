<?php

namespace Tests\Feature\Jobs;

use App\Contracts\Events\DiscordMessageEvent;
use App\Jobs\SendDiscordNotification;
use App\Notifications\DiscordNotification;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use NotificationChannels\Discord\DiscordMessage;
use Tests\TestCase;

class SendDiscordNotificationTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testSendDiscordNotificationJobSendsNotification()
    {
        Notification::fake();

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

        $job->handle();

        Notification::assertSentTo(
            new AnonymousNotifiable,
            DiscordNotification::class,
        );
    }
}
