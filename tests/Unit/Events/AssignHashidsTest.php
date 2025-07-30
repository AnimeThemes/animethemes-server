<?php

declare(strict_types=1);

use App\Contracts\Events\AssignHashidsEvent;
use App\Listeners\AssignHashids;
use Illuminate\Support\Facades\Event;

test('listening', function () {
    Event::assertListening(AssignHashidsEvent::class, AssignHashids::class);
});
