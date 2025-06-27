<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Pivot\Wiki;

use App\Constants\FeatureConstants;
use App\Events\Pivot\Wiki\ArtistResource\ArtistResourceCreated;
use App\Events\Pivot\Wiki\ArtistResource\ArtistResourceDeleted;
use App\Events\Pivot\Wiki\ArtistResource\ArtistResourceUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\ArtistResource;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
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
    public function test_artist_resource_created_sends_discord_notification(): void
    {
        $artist = Artist::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(ArtistResourceCreated::class);

        $artist->resources()->attach($resource);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an Artist is detached from a Resource or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function test_artist_resource_deleted_sends_discord_notification(): void
    {
        $artist = Artist::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $artist->resources()->attach($resource);

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(ArtistResourceDeleted::class);

        $artist->resources()->detach($resource);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an Artist Resource pivot is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function test_artist_resource_updated_sends_discord_notification(): void
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

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(ArtistResourceUpdated::class);

        $artistResource->fill($changes->getAttributes());
        $artistResource->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
