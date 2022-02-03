<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Wiki;

use App\Events\Wiki\Anime\AnimeCreated;
use App\Events\Wiki\Anime\AnimeDeleted;
use App\Events\Wiki\Anime\AnimeRestored;
use App\Events\Wiki\Anime\AnimeUpdated;
use App\Models\Wiki\Anime;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class AnimeTest.
 */
class AnimeTest extends TestCase
{
    /**
     * When an Anime is created, an AnimeCreated event shall be dispatched.
     *
     * @return void
     */
    public function testAnimeCreatedEventDispatched()
    {
        Event::fake();

        Anime::factory()->createOne();

        Event::assertDispatched(AnimeCreated::class);
    }

    /**
     * When an Anime is deleted, an AnimeDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testAnimeDeletedEventDispatched()
    {
        Event::fake();

        $anime = Anime::factory()->createOne();

        $anime->delete();

        Event::assertDispatched(AnimeDeleted::class);
    }

    /**
     * When an Anime is restored, an AnimeRestored event shall be dispatched.
     *
     * @return void
     */
    public function testAnimeRestoredEventDispatched()
    {
        Event::fake();

        $anime = Anime::factory()->createOne();

        $anime->restore();

        Event::assertDispatched(AnimeRestored::class);
    }

    /**
     * When an Anime is restored, an AnimeUpdated event shall not be dispatched.
     * Note: This is a customization that overrides default framework behavior.
     * An updated event is fired on restore.
     *
     * @return void
     */
    public function testAnimeRestoresQuietly()
    {
        Event::fake();

        $anime = Anime::factory()->createOne();

        $anime->restore();

        Event::assertNotDispatched(AnimeUpdated::class);
    }

    /**
     * When an Anime is updated, an AnimeUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testAnimeUpdatedEventDispatched()
    {
        Event::fake();

        $anime = Anime::factory()->createOne();
        $changes = Anime::factory()->makeOne();

        $anime->fill($changes->getAttributes());
        $anime->save();

        Event::assertDispatched(AnimeUpdated::class);
    }
}
