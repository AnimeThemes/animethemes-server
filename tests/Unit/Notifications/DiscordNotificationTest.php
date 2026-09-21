<?php

declare(strict_types=1);

use App\Notifications\DiscordNotification;
use Illuminate\Notifications\AnonymousNotifiable;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;

test('via discord message', function (): void {
    $message = DiscordMessage::create();

    $notification = new DiscordNotification($message);

    expect($notification->via(new AnonymousNotifiable()))->toEqual([DiscordChannel::class]);
});

test('to discord message', function (): void {
    $message = DiscordMessage::create();

    $notification = new DiscordNotification($message);

    expect($notification->toDiscord(new AnonymousNotifiable()))->toBeInstanceOf(DiscordMessage::class);
});
