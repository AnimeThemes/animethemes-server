<?php

declare(strict_types=1);

use App\Contracts\Events\UpdatePlaylistTracksEvent;
use App\Listeners\List\UpdatePlaylistTracks;
use Illuminate\Support\Facades\Event;

test('listening', function () {
    Event::assertListening(UpdatePlaylistTracksEvent::class, UpdatePlaylistTracks::class);
});
