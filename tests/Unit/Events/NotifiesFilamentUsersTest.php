<?php

declare(strict_types=1);

use App\Contracts\Events\FilamentNotificationEvent;
use App\Listeners\NotifiesFilamentUsers;
use Illuminate\Support\Facades\Event;

test('listening', function () {
    Event::assertListening(FilamentNotificationEvent::class, NotifiesFilamentUsers::class);
});
