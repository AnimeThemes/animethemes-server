<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Wiki;

use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Song;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class SongTest.
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
        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        Song::factory()->create();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a song is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testSongDeletedSendsDiscordNotification()
    {
        $song = Song::factory()->create();

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $song->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a song is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testSongRestoredSendsDiscordNotification()
    {
        $song = Song::factory()->create();

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $song->restore();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a song is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testSongUpdatedSendsDiscordNotification()
    {
        $song = Song::factory()->create();

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $changes = Song::factory()->make();

        $song->fill($changes->getAttributes());
        $song->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
