<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Pivot\Wiki;

use App\Constants\FeatureConstants;
use App\Events\Pivot\Wiki\SongResource\SongResourceCreated;
use App\Events\Pivot\Wiki\SongResource\SongResourceDeleted;
use App\Events\Pivot\Wiki\SongResource\SongResourceUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Song;
use App\Pivots\Wiki\SongResource;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Tests\TestCase;

/**
 * Class SongResourceTest.
 */
class SongResourceTest extends TestCase
{
    /**
     * When an Song is attached to a Resource or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testSongResourceCreatedSendsDiscordNotification(): void
    {
        $song = Song::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(SongResourceCreated::class);

        $song->resources()->attach($resource);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an Song is detached from a Resource or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testSongResourceDeletedSendsDiscordNotification(): void
    {
        $song = Song::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $song->resources()->attach($resource);

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(SongResourceDeleted::class);

        $song->resources()->detach($resource);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an Song Resource pivot is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testSongResourceUpdatedSendsDiscordNotification(): void
    {
        $song = Song::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $songResource = SongResource::factory()
            ->for($song, 'song')
            ->for($resource, 'resource')
            ->createOne();

        $changes = SongResource::factory()
            ->for($song, 'song')
            ->for($resource, 'resource')
            ->makeOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(SongResourceUpdated::class);

        $songResource->fill($changes->getAttributes());
        $songResource->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
