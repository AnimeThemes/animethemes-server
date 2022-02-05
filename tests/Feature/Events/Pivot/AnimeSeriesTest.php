<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Pivot;

use App\Events\Pivot\AnimeSeries\AnimeSeriesCreated;
use App\Events\Pivot\AnimeSeries\AnimeSeriesDeleted;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class AnimeSeriesTest.
 */
class AnimeSeriesTest extends TestCase
{
    /**
     * When an Anime is attached to a Series or vice versa, an AnimeSeriesCreated event shall be dispatched.
     *
     * @return void
     */
    public function testAnimeSeriesCreatedEventDispatched(): void
    {
        Event::fake();

        $anime = Anime::factory()->createOne();
        $series = Series::factory()->createOne();

        $anime->series()->attach($series);

        Event::assertDispatched(AnimeSeriesCreated::class);
    }

    /**
     * When an Anime is detached from a Series or vice versa, an AnimeSeriesDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testAnimeSeriesDeletedEventDispatched(): void
    {
        Event::fake();

        $anime = Anime::factory()->createOne();
        $series = Series::factory()->createOne();

        $anime->series()->attach($series);
        $anime->series()->detach($series);

        Event::assertDispatched(AnimeSeriesDeleted::class);
    }
}
