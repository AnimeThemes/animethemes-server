<?php

declare(strict_types=1);

use App\Notifications\DiscordNotification;
use Illuminate\Notifications\AnonymousNotifiable;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;

test('via discord message', function () {
    $message = DiscordMessage::create();

    $notification = new DiscordNotification($message);

    $this->assertEquals([DiscordChannel::class], $notification->via(new AnonymousNotifiable()));
});

test('to discord message', function () {
    $message = DiscordMessage::create();

    $notification = new DiscordNotification($message);

    $this->assertInstanceOf(DiscordMessage::class, $notification->toDiscord(new AnonymousNotifiable()));
});
