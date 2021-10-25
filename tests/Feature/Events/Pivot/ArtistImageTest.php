<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Pivot;

use App\Events\Pivot\ArtistImage\ArtistImageCreated;
use App\Events\Pivot\ArtistImage\ArtistImageDeleted;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class ArtistImageTest.
 */
class ArtistImageTest extends TestCase
{
    /**
     * When an Artist is attached to an Image or vice versa, an ArtistImageCreated event shall be dispatched.
     *
     * @return void
     */
    public function testArtistImageCreatedEventDispatched()
    {
        Event::fake();

        $artist = Artist::factory()->createOne();
        $image = Image::factory()->createOne();

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

        $artist = Artist::factory()->createOne();
        $image = Image::factory()->createOne();

        $artist->images()->attach($image);
        $artist->images()->detach($image);

        Event::assertDispatched(ArtistImageDeleted::class);
    }
}
