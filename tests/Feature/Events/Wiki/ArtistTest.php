<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Wiki;

use App\Events\Wiki\Artist\ArtistCreated;
use App\Events\Wiki\Artist\ArtistDeleted;
use App\Events\Wiki\Artist\ArtistRestored;
use App\Events\Wiki\Artist\ArtistUpdated;
use App\Models\Wiki\Artist;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class ArtistTest.
 */
class ArtistTest extends TestCase
{
    /**
     * When an Artist is created, an ArtistCreated event shall be dispatched.
     *
     * @return void
     */
    public function testArtistCreatedEventDispatched(): void
    {
        Event::fake();

        Artist::factory()->createOne();

        Event::assertDispatched(ArtistCreated::class);
    }

    /**
     * When an Artist is deleted, an ArtistDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testArtistDeletedEventDispatched(): void
    {
        Event::fake();

        $artist = Artist::factory()->createOne();

        $artist->delete();

        Event::assertDispatched(ArtistDeleted::class);
    }

    /**
     * When an Artist is restored, an ArtistRestored event shall be dispatched.
     *
     * @return void
     */
    public function testArtistRestoredEventDispatched(): void
    {
        Event::fake();

        $artist = Artist::factory()->createOne();

        $artist->restore();

        Event::assertDispatched(ArtistRestored::class);
    }

    /**
     * When an Artist is restored, an ArtistUpdated event shall not be dispatched.
     * Note: This is a customization that overrides default framework behavior.
     * An updated event is fired on restore.
     *
     * @return void
     */
    public function testArtistRestoresQuietly(): void
    {
        Event::fake();

        $artist = Artist::factory()->createOne();

        $artist->restore();

        Event::assertNotDispatched(ArtistUpdated::class);
    }

    /**
     * When an Artist is updated, an ArtistUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testArtistUpdatedEventDispatched(): void
    {
        Event::fake();

        $artist = Artist::factory()->createOne();
        $changes = Artist::factory()->makeOne();

        $artist->fill($changes->getAttributes());
        $artist->save();

        Event::assertDispatched(ArtistUpdated::class);
    }
}
