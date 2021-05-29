<?php

namespace Tests\Feature\Events\Pivot;

use App\Events\Pivot\ArtistResource\ArtistResourceCreated;
use App\Events\Pivot\ArtistResource\ArtistResourceDeleted;
use App\Events\Pivot\ArtistResource\ArtistResourceUpdated;
use App\Models\Artist;
use App\Models\ExternalResource;
use App\Pivots\ArtistResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ArtistResourceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When an Artist is attached to a Resource or vice versa, an ArtistResourceCreated event shall be dispatched.
     *
     * @return void
     */
    public function testArtistResourceCreatedEventDispatched()
    {
        Event::fake();

        $artist = Artist::factory()->create();
        $resource = ExternalResource::factory()->create();

        $artist->externalResources()->attach($resource);

        Event::assertDispatched(ArtistResourceCreated::class);
    }

    /**
     * When an Artist is detached from a Resource or vice versa, an ArtistResourceDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testArtistResourceDeletedEventDispatched()
    {
        Event::fake();

        $artist = Artist::factory()->create();
        $resource = ExternalResource::factory()->create();

        $artist->externalResources()->attach($resource);
        $artist->externalResources()->detach($resource);

        Event::assertDispatched(ArtistResourceDeleted::class);
    }

    /**
     * When an Artist Resource pivot is updated, an ArtistResourceUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testArtistResourceUpdatedEventDispatched()
    {
        Event::fake();

        $artist = Artist::factory()->create();
        $resource = ExternalResource::factory()->create();

        $artistResource = ArtistResource::factory()
            ->for($artist, 'artist')
            ->for($resource, 'resource')
            ->create();

        $changes = ArtistResource::factory()
            ->for($artist, 'artist')
            ->for($resource, 'resource')
            ->make();

        $artistResource->fill($changes->getAttributes());
        $artistResource->save();

        Event::assertDispatched(ArtistResourceUpdated::class);
    }
}
