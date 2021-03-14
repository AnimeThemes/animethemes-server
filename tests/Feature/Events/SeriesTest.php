<?php

namespace Tests\Feature\Events;

use App\Events\Series\SeriesCreated;
use App\Events\Series\SeriesDeleted;
use App\Events\Series\SeriesRestored;
use App\Events\Series\SeriesUpdated;
use App\Models\Series;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class SeriesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * When a Series is created, a SeriesCreated event shall be dispatched.
     *
     * @return void
     */
    public function testSeriesCreatedEventDispatched()
    {
        Event::fake();

        Series::factory()->create();

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

        $series = Series::factory()->create();

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

        $series = Series::factory()->create();

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

        $series = Series::factory()->create();
        $changes = Series::factory()->make();

        $series->fill($changes->getAttributes());
        $series->save();

        Event::assertDispatched(SeriesUpdated::class);
    }
}
