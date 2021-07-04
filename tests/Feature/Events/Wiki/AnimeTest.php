<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Wiki;

use App\Events\Wiki\Anime\AnimeCreated;
use App\Events\Wiki\Anime\AnimeDeleted;
use App\Events\Wiki\Anime\AnimeRestored;
use App\Events\Wiki\Anime\AnimeUpdated;
use App\Models\Wiki\Anime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class AnimeTest.
 */
class AnimeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When an Anime is created, an AnimeCreated event shall be dispatched.
     *
     * @return void
     */
    public function testAnimeCreatedEventDispatched()
    {
        Event::fake();

        Anime::factory()->create();

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

        $anime = Anime::factory()->create();

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

        $anime = Anime::factory()->create();

        $anime->restore();

        Event::assertDispatched(AnimeRestored::class);
    }

    /**
     * When an Anime is updated, an AnimeUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testAnimeUpdatedEventDispatched()
    {
        Event::fake();

        $anime = Anime::factory()->create();
        $changes = Anime::factory()->make();

        $anime->fill($changes->getAttributes());
        $anime->save();

        Event::assertDispatched(AnimeUpdated::class);
    }
}
