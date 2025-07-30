<?php

declare(strict_types=1);

use App\Contracts\Events\SyncArtistSongEvent;
use App\Listeners\SyncArtistSong;
use Illuminate\Support\Facades\Event;

test('listening', function () {
    Event::assertListening(SyncArtistSongEvent::class, SyncArtistSong::class);
});
