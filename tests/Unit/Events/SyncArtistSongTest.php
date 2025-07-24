<?php

declare(strict_types=1);

namespace Tests\Unit\Events;

use App\Contracts\Events\SyncArtistSongEvent;
use App\Listeners\SyncArtistSong;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class SyncArtistSongTest extends TestCase
{
    /**
     * SyncArtistSong shall listen to SyncArtistSongEvent.
     */
    public function testListening(): void
    {
        Event::assertListening(SyncArtistSongEvent::class, SyncArtistSong::class);
    }
}
