<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Wiki;

use App\Events\Wiki\Series\SeriesCreated;
use App\Events\Wiki\Series\SeriesDeleted;
use App\Events\Wiki\Series\SeriesRestored;
use App\Events\Wiki\Series\SeriesUpdated;
use App\Models\Wiki\Series;
use Illuminate\Support\Arr;
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
    public function testSeriesCreatedEventDispatched(): void
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
    public function testSeriesDeletedEventDispatched(): void
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
    public function testSeriesRestoredEventDispatched(): void
    {
        Event::fake();

        $series = Series::factory()->createOne();

        $series->restore();

        Event::assertDispatched(SeriesRestored::class);
    }

    /**
     * When a Series is restored, a SeriesUpdated event shall not be dispatched.
     * Note: This is a customization that overrides default framework behavior.
     * An updated event is fired on restore.
     *
     * @return void
     */
    public function testSeriesRestoresQuietly(): void
    {
        Event::fake();

        $series = Series::factory()->createOne();

        $series->restore();

        Event::assertNotDispatched(SeriesUpdated::class);
    }

    /**
     * When a Series is updated, a SeriesUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testSeriesUpdatedEventDispatched(): void
    {
        Event::fake();

        $series = Series::factory()->createOne();
        $changes = Series::factory()->makeOne();

        $series->fill($changes->getAttributes());
        $series->save();

        Event::assertDispatched(SeriesUpdated::class);
    }

    /**
     * The SeriesUpdated event shall contain embed fields.
     *
     * @return void
     */
    public function testSeriesUpdatedEventEmbedFields(): void
    {
        Event::fake();

        $anime = Series::factory()->createOne();
        $changes = Series::factory()->makeOne();

        $anime->fill($changes->getAttributes());
        $anime->save();

        Event::assertDispatched(SeriesUpdated::class, function (SeriesUpdated $event) {
            $message = $event->getDiscordMessage();

            return ! empty(Arr::get($message->embed, 'fields'));
        });
    }
}
