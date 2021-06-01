<?php

declare(strict_types=1);

namespace Jobs\Pivot;

use App\Jobs\SendDiscordNotification;
use App\Models\Artist;
use App\Models\ExternalResource;
use App\Pivots\ArtistResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class ArtistResourceTest.
 */
class ArtistResourceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When an Artist is attached to a Resource or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testArtistResourceCreatedSendsDiscordNotification()
    {
        $artist = Artist::factory()->create();
        $resource = ExternalResource::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $artist->externalResources()->attach($resource);

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When an Artist is detached from a Resource or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testArtistResourceDeletedSendsDiscordNotification()
    {
        $artist = Artist::factory()->create();
        $resource = ExternalResource::factory()->create();
        $artist->externalResources()->attach($resource);

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $artist->externalResources()->detach($resource);

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When an Artist Resource pivot is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testArtistResourceUpdatedSendsDiscordNotification()
    {
        $artist = Artist::factory()->create();
        $resource = ExternalResource::factory()->create();

        $artistResource = ArtistResource::factory()
            ->for($artist, 'artist')
            ->for($resource, 'resource')
            ->create();

        $changes = ArtistResource::factory()
            ->for($artist, 'artist')
            ->for($resource, 'resource')
            ->make();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $artistResource->fill($changes->getAttributes());
        $artistResource->save();

        Bus::assertDispatched(SendDiscordNotification::class);
    }
}
