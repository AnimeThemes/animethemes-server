<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Wiki;

use App\Events\Wiki\Song\SongCreated;
use App\Events\Wiki\Song\SongDeleted;
use App\Events\Wiki\Song\SongRestored;
use App\Events\Wiki\Song\SongUpdated;
use App\Models\Wiki\Song;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class SongTest.
 */
class SongTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When a Song is created, a SongCreated event shall be dispatched.
     *
     * @return void
     */
    public function testSongCreatedEventDispatched()
    {
        Event::fake();

        Song::factory()->create();

        Event::assertDispatched(SongCreated::class);
    }

    /**
     * When a Song is deleted, a SongDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testSongDeletedEventDispatched()
    {
        Event::fake();

        $song = Song::factory()->create();

        $song->delete();

        Event::assertDispatched(SongDeleted::class);
    }

    /**
     * When a Song is restored, a SongRestored event shall be dispatched.
     *
     * @return void
     */
    public function testSongRestoredEventDispatched()
    {
        Event::fake();

        $song = Song::factory()->create();

        $song->restore();

        Event::assertDispatched(SongRestored::class);
    }

    /**
     * When a Song is updated, a SongUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testSongUpdatedEventDispatched()
    {
        Event::fake();

        $song = Song::factory()->create();
        $changes = Song::factory()->make();

        $song->fill($changes->getAttributes());
        $song->save();

        Event::assertDispatched(SongUpdated::class);
    }
}