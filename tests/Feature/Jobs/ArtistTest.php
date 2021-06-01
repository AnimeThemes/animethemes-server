<?php

declare(strict_types=1);

namespace Jobs;

use App\Jobs\SendDiscordNotification;
use App\Models\Artist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class ArtistTest
 * @package Jobs
 */
class ArtistTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When an artist is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testArtistCreatedSendsDiscordNotification()
    {
        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        Artist::factory()->create();

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When an artist is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testArtistDeletedSendsDiscordNotification()
    {
        $artist = Artist::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $artist->delete();

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When an artist is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testArtistRestoredSendsDiscordNotification()
    {
        $artist = Artist::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $artist->restore();

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When an artist is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testArtistUpdatedSendsDiscordNotification()
    {
        $artist = Artist::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $changes = Artist::factory()->make();

        $artist->fill($changes->getAttributes());
        $artist->save();

        Bus::assertDispatched(SendDiscordNotification::class);
    }
}
