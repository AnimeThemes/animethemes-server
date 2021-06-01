<?php

declare(strict_types=1);

namespace Events\Pivot;

use App\Events\Pivot\ArtistImage\ArtistImageCreated;
use App\Events\Pivot\ArtistImage\ArtistImageDeleted;
use App\Models\Artist;
use App\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class ArtistImageTest.
 */
class ArtistImageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When an Artist is attached to an Image or vice versa, an ArtistImageCreated event shall be dispatched.
     *
     * @return void
     */
    public function testArtistImageCreatedEventDispatched()
    {
        Event::fake();

        $artist = Artist::factory()->create();
        $image = Image::factory()->create();

        $artist->images()->attach($image);

        Event::assertDispatched(ArtistImageCreated::class);
    }

    /**
     * When an Artist is detached from an Image or vice versa, an ArtistImageDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testArtistImageDeletedEventDispatched()
    {
        Event::fake();

        $artist = Artist::factory()->create();
        $image = Image::factory()->create();

        $artist->images()->attach($image);
        $artist->images()->detach($image);

        Event::assertDispatched(ArtistImageDeleted::class);
    }
}
