<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Pivot\Wiki;

use App\Constants\FeatureConstants;
use App\Events\Pivot\Wiki\StudioResource\StudioResourceCreated;
use App\Events\Pivot\Wiki\StudioResource\StudioResourceDeleted;
use App\Events\Pivot\Wiki\StudioResource\StudioResourceUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\StudioResource;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class StudioResourceTest extends TestCase
{
    /**
     * When a Studio is attached to a Resource or vice versa, a SendDiscordNotification job shall be dispatched.
     */
    public function testStudioResourceCreatedSendsDiscordNotification(): void
    {
        $studio = Studio::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(StudioResourceCreated::class);

        $studio->resources()->attach($resource);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a Studio is detached from a Resource or vice versa, a SendDiscordNotification job shall be dispatched.
     */
    public function testStudioResourceDeletedSendsDiscordNotification(): void
    {
        $studio = Studio::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $studio->resources()->attach($resource);

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(StudioResourceDeleted::class);

        $studio->resources()->detach($resource);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a Studio Resource pivot is updated, a SendDiscordNotification job will be dispatched.
     */
    public function testStudioResourceUpdatedSendsDiscordNotification(): void
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

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(StudioResourceUpdated::class);

        $studioResource->fill($changes->getAttributes());
        $studioResource->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
