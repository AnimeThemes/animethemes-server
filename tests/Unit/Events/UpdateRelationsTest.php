<?php

declare(strict_types=1);

use App\Contracts\Events\UpdateRelationsEvent;
use App\Listeners\UpdateRelations;
use Illuminate\Support\Facades\Event;

test('listening', function () {
    Event::assertListening(UpdateRelationsEvent::class, UpdateRelations::class);
});
