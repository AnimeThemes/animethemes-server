<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Pivot;

use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;
use App\Pivots\StudioResource;

/**
 * Class StudioResourceTest.
 */
class StudioResourceTest extends TestCase
{
    /**
     * When a Studio is attached to a Resource or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testStudioResourceCreatedSendsDiscordNotification()
    {
        $studio = Studio::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $studio->resources()->attach($resource);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a Studio is detached from a Resource or vice versa, a SendDiscordNotification job shall be dispatched.
     */
    public function testStudioResourceDeletedSendsDiscordNotification()
    {
        $studio = Studio::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $studio->resources()->attach($resource);

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $studio->resources()->detach($resource);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a Studio Resource pivot is updated, a SendDiscordNotification job will be dispatched.
     */
    public function testStudioResourceUpdatedSendsDiscordNotification()
    {
        $studio = Studio::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $studioResource = StudioResource::factory()
            ->for($studio, 'studio')
            ->for($resource, 'resource')
            ->createOne();

        $changes = StudioResource::factory()
            ->for($studio, 'studio')
            ->for($resource, 'resource')
            ->makeOne();

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $studioResource->fill($changes->getAttributes());
        $studioResource->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
