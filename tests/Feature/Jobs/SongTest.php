<?php declare(strict_types=1);

namespace Jobs;

use App\Jobs\SendDiscordNotification;
use App\Models\Song;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class SongTest
 * @package Jobs
 */
class SongTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When a song is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testSongCreatedSendsDiscordNotification()
    {
        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        Song::factory()->create();

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When a song is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testSongDeletedSendsDiscordNotification()
    {
        $song = Song::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $song->delete();

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When a song is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testSongRestoredSendsDiscordNotification()
    {
        $song = Song::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $song->restore();

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When a song is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testSongUpdatedSendsDiscordNotification()
    {
        $song = Song::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $changes = Song::factory()->make();

        $song->fill($changes->getAttributes());
        $song->save();

        Bus::assertDispatched(SendDiscordNotification::class);
    }
}
