<?php declare(strict_types=1);

namespace Jobs;

use App\Jobs\SendDiscordNotification;
use App\Models\Series;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class SeriesTest
 * @package Jobs
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
        Bus::fake(SendDiscordNotification::class);

        Series::factory()->create();

        Bus::assertDispatched(SendDiscordNotification::class);
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
        Bus::fake(SendDiscordNotification::class);

        $series->delete();

        Bus::assertDispatched(SendDiscordNotification::class);
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
        Bus::fake(SendDiscordNotification::class);

        $series->restore();

        Bus::assertDispatched(SendDiscordNotification::class);
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
        Bus::fake(SendDiscordNotification::class);

        $changes = Series::factory()->make();

        $series->fill($changes->getAttributes());
        $series->save();

        Bus::assertDispatched(SendDiscordNotification::class);
    }
}
