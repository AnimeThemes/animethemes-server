<?php

namespace Tests\Feature\Events;

use App\Events\Song\SongCreated;
use App\Events\Song\SongDeleted;
use App\Events\Song\SongUpdated;
use App\Models\Song;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class SongTest extends TestCase
{
    use RefreshDatabase, WithFaker;

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
