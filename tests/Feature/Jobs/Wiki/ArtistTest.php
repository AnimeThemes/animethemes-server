<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Wiki;

use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Artist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class ArtistTest.
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
        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        Artist::factory()->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an artist is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testArtistDeletedSendsDiscordNotification()
    {
        $artist = Artist::factory()->createOne();

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $artist->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an artist is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testArtistRestoredSendsDiscordNotification()
    {
        $artist = Artist::factory()->createOne();

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $artist->restore();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an artist is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testArtistUpdatedSendsDiscordNotification()
    {
        $artist = Artist::factory()->createOne();

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $changes = Artist::factory()->makeOne();

        $artist->fill($changes->getAttributes());
        $artist->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
