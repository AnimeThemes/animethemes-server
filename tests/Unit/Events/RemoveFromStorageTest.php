<?php

declare(strict_types=1);

use App\Contracts\Events\RemoveFromStorageEvent;
use App\Listeners\Storage\RemoveFromStorage;
use Illuminate\Support\Facades\Event;

test('listening', function () {
    Event::assertListening(RemoveFromStorageEvent::class, RemoveFromStorage::class);
});
