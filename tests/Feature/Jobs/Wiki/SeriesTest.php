<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Wiki;

use App\Constants\Config\FlagConstants;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Series;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class SeriesTest.
 */
class SeriesTest extends TestCase
{
    /**
     * When a series is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testSeriesCreatedSendsDiscordNotification(): void
    {
        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        Series::factory()->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a series is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testSeriesDeletedSendsDiscordNotification(): void
    {
        $series = Series::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $series->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a series is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testSeriesRestoredSendsDiscordNotification(): void
    {
        $series = Series::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $series->restore();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a series is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testSeriesUpdatedSendsDiscordNotification(): void
    {
        $series = Series::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $changes = Series::factory()->makeOne();

        $series->fill($changes->getAttributes());
        $series->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
