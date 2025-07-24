<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Wiki;

use App\Constants\FeatureConstants;
use App\Events\Wiki\Series\SeriesCreated;
use App\Events\Wiki\Series\SeriesDeleted;
use App\Events\Wiki\Series\SeriesRestored;
use App\Events\Wiki\Series\SeriesUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Series;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class SeriesTest extends TestCase
{
    /**
     * When a series is created, a SendDiscordNotification job shall be dispatched.
     */
    public function testSeriesCreatedSendsDiscordNotification(): void
    {
        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(SeriesCreated::class);

        Series::factory()->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a series is deleted, a SendDiscordNotification job shall be dispatched.
     */
    public function testSeriesDeletedSendsDiscordNotification(): void
    {
        $series = Series::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(SeriesDeleted::class);

        $series->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a series is restored, a SendDiscordNotification job shall be dispatched.
     */
    public function testSeriesRestoredSendsDiscordNotification(): void
    {
        $series = Series::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(SeriesRestored::class);

        $series->restore();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a series is updated, a SendDiscordNotification job shall be dispatched.
     */
    public function testSeriesUpdatedSendsDiscordNotification(): void
    {
        $series = Series::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(SeriesUpdated::class);

        $changes = Series::factory()->makeOne();

        $series->fill($changes->getAttributes());
        $series->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
