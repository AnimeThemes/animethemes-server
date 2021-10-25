<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Wiki;

use App\Events\Wiki\Series\SeriesCreated;
use App\Events\Wiki\Series\SeriesDeleted;
use App\Events\Wiki\Series\SeriesRestored;
use App\Events\Wiki\Series\SeriesUpdated;
use App\Models\Wiki\Series;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class SeriesTest.
 */
class SeriesTest extends TestCase
{

    /**
     * When a Series is created, a SeriesCreated event shall be dispatched.
     *
     * @return void
     */
    public function testSeriesCreatedEventDispatched()
    {
        Event::fake();

        Series::factory()->createOne();

        Event::assertDispatched(SeriesCreated::class);
    }

    /**
     * When a Series is deleted, a SeriesDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testSeriesDeletedEventDispatched()
    {
        Event::fake();

        $series = Series::factory()->createOne();

        $series->delete();

        Event::assertDispatched(SeriesDeleted::class);
    }

    /**
     * When a Series is restored, a SeriesRestored event shall be dispatched.
     *
     * @return void
     */
    public function testSeriesRestoredEventDispatched()
    {
        Event::fake();

        $series = Series::factory()->createOne();

        $series->restore();

        Event::assertDispatched(SeriesRestored::class);
    }

    /**
     * When a Series is updated, a SeriesUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testSeriesUpdatedEventDispatched()
    {
        Event::fake();

        $series = Series::factory()->createOne();
        $changes = Series::factory()->makeOne();

        $series->fill($changes->getAttributes());
        $series->save();

        Event::assertDispatched(SeriesUpdated::class);
    }
}
