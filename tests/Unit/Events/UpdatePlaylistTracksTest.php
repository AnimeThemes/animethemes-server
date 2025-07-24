<?php

declare(strict_types=1);

namespace Tests\Unit\Events;

use App\Contracts\Events\UpdatePlaylistTracksEvent;
use App\Listeners\List\UpdatePlaylistTracks;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UpdatePlaylistTracksTest extends TestCase
{
    /**
     * UpdatePlaylistTracks shall listen to UpdatePlaylistTracksEvent.
     */
    public function testListening(): void
    {
        Event::assertListening(UpdatePlaylistTracksEvent::class, UpdatePlaylistTracks::class);
    }
}
