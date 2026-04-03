<?php

declare(strict_types=1);

use App\Contracts\Events\CreateArtistSongEvent;
use App\Listeners\CreateArtistSong;
use Illuminate\Support\Facades\Event;

test('listening', function () {
    Event::assertListening(CreateArtistSongEvent::class, CreateArtistSong::class);
});
