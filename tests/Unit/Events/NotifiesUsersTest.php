<?php

declare(strict_types=1);

use App\Contracts\Events\NotifiesUsersEvent;
use App\Listeners\NotifiesUsers;
use Illuminate\Support\Facades\Event;

test('listening', function () {
    Event::assertListening(NotifiesUsersEvent::class, NotifiesUsers::class);
});
