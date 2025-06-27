<?php

declare(strict_types=1);

namespace Tests\Unit\Events;

use App\Contracts\Events\UpdatePlaylistTracksEvent;
use App\Listeners\List\UpdatePlaylistTracks;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class UpdatePlaylistTracksTest.
 */
class UpdatePlaylistTracksTest extends TestCase
{
    /**
     * UpdatePlaylistTracks shall listen to UpdatePlaylistTracksEvent.
     *
     * @return void
     */
    public function test_listening(): void
    {
        Event::assertListening(UpdatePlaylistTracksEvent::class, UpdatePlaylistTracks::class);
    }
}
