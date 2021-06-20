<?php

declare(strict_types=1);

namespace Jobs\Wiki;

use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Series;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class SeriesTest.
 */
class SeriesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When a series is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testSeriesCreatedSendsDiscordNotification()
    {
        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        Series::factory()->create();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a series is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testSeriesDeletedSendsDiscordNotification()
    {
        $series = Series::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $series->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a series is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testSeriesRestoredSendsDiscordNotification()
    {
        $series = Series::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $series->restore();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a series is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testSeriesUpdatedSendsDiscordNotification()
    {
        $series = Series::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $changes = Series::factory()->make();

        $series->fill($changes->getAttributes());
        $series->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
