<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Pivot;

use App\Constants\Config\FlagConstants;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Pivots\ArtistResource;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class ArtistResourceTest.
 */
class ArtistResourceTest extends TestCase
{
    /**
     * When an Artist is attached to a Resource or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testArtistResourceCreatedSendsDiscordNotification(): void
    {
        $artist = Artist::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $artist->resources()->attach($resource);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an Artist is detached from a Resource or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testArtistResourceDeletedSendsDiscordNotification(): void
    {
        $artist = Artist::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $artist->resources()->attach($resource);

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $artist->resources()->detach($resource);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an Artist Resource pivot is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testArtistResourceUpdatedSendsDiscordNotification(): void
    {
        $artist = Artist::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $artistResource = ArtistResource::factory()
            ->for($artist, 'artist')
            ->for($resource, 'resource')
            ->createOne();

        $changes = ArtistResource::factory()
            ->for($artist, 'artist')
            ->for($resource, 'resource')
            ->makeOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $artistResource->fill($changes->getAttributes());
        $artistResource->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
