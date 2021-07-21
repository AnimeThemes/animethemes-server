<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Pivot;

use App\Events\Pivot\ArtistResource\ArtistResourceCreated;
use App\Events\Pivot\ArtistResource\ArtistResourceDeleted;
use App\Events\Pivot\ArtistResource\ArtistResourceUpdated;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Pivots\ArtistResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class ArtistResourceTest.
 */
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

        $artist = Artist::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $artist->resources()->attach($resource);

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

        $artist = Artist::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $artist->resources()->attach($resource);
        $artist->resources()->detach($resource);

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

        $artist = Artist::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $artistResource = ArtistResource::factory()
            ->for($artist, 'artist')
            ->for($resource, 'resource')
            ->createOne();

        $changes = ArtistResource::factory()
            ->for($artist, 'artist')
            ->for($resource, 'resource')
            ->makeOne();

        $artistResource->fill($changes->getAttributes());
        $artistResource->save();

        Event::assertDispatched(ArtistResourceUpdated::class);
    }
}
