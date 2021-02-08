<?php

namespace Tests\Feature\Events;

use App\Events\Artist\ArtistCreated;
use App\Events\Artist\ArtistDeleted;
use App\Events\Artist\ArtistUpdated;
use App\Models\Artist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ArtistTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * When an Artist is created, an ArtistCreated event shall be dispatched.
     *
     * @return void
     */
    public function testArtistCreatedEventDispatched()
    {
        Event::fake();

        Artist::factory()->create();

        Event::assertDispatched(ArtistCreated::class);
    }

    /**
     * When an Artist is deleted, an ArtistDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testArtistDeletedEventDispatched()
    {
        Event::fake();

        $artist = Artist::factory()->create();

        $artist->delete();

        Event::assertDispatched(ArtistDeleted::class);
    }

    /**
     * When an Artist is updated, an ArtistUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testArtistUpdatedEventDispatched()
    {
        Event::fake();

        $artist = Artist::factory()->create();
        $changes = Artist::factory()->make();

        $artist->fill($changes->getAttributes());
        $artist->save();

        Event::assertDispatched(ArtistUpdated::class);
    }
}
