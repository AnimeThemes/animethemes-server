<?php

declare(strict_types=1);

use App\Contracts\Events\ManagesTrackEvent;
use App\Listeners\ManagesTrack;
use Illuminate\Support\Facades\Event;

test('listening', function () {
    Event::assertListening(ManagesTrackEvent::class, ManagesTrack::class);
});
