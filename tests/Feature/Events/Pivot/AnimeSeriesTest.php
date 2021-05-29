<?php

namespace Tests\Feature\Events\Pivot;

use App\Events\Pivot\AnimeSeries\AnimeSeriesCreated;
use App\Events\Pivot\AnimeSeries\AnimeSeriesDeleted;
use App\Models\Anime;
use App\Models\Series;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class AnimeSeriesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When an Anime is attached to a Series or vice versa, an AnimeSeriesCreated event shall be dispatched.
     *
     * @return void
     */
    public function testAnimeSeriesCreatedEventDispatched()
    {
        Event::fake();

        $anime = Anime::factory()->create();
        $series = Series::factory()->create();

        $anime->series()->attach($series);

        Event::assertDispatched(AnimeSeriesCreated::class);
    }

    /**
     * When an Anime is detached from a Series or vice versa, an AnimeSeriesDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testAnimeSeriesDeletedEventDispatched()
    {
        Event::fake();

        $anime = Anime::factory()->create();
        $series = Series::factory()->create();

        $anime->series()->attach($series);
        $anime->series()->detach($series);

        Event::assertDispatched(AnimeSeriesDeleted::class);
    }
}
