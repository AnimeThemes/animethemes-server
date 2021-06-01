<?php

declare(strict_types=1);

namespace Events\Pivot;

use App\Events\Pivot\AnimeImage\AnimeImageCreated;
use App\Events\Pivot\AnimeImage\AnimeImageDeleted;
use App\Models\Anime;
use App\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class AnimeImageTest.
 */
class AnimeImageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When an Anime is attached to an Image or vice versa, an AnimeImageCreated event shall be dispatched.
     *
     * @return void
     */
    public function testAnimeImageCreatedEventDispatched()
    {
        Event::fake();

        $anime = Anime::factory()->create();
        $image = Image::factory()->create();

        $anime->images()->attach($image);

        Event::assertDispatched(AnimeImageCreated::class);
    }

    /**
     * When an Anime is detached from an Image or vice versa, an AnimeImageDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testAnimeImageDeletedEventDispatched()
    {
        Event::fake();

        $anime = Anime::factory()->create();
        $image = Image::factory()->create();

        $anime->images()->attach($image);
        $anime->images()->detach($image);

        Event::assertDispatched(AnimeImageDeleted::class);
    }
}
