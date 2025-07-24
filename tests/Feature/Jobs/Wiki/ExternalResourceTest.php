<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Wiki;

use App\Constants\FeatureConstants;
use App\Events\Wiki\ExternalResource\ExternalResourceCreated;
use App\Events\Wiki\ExternalResource\ExternalResourceDeleted;
use App\Events\Wiki\ExternalResource\ExternalResourceRestored;
use App\Events\Wiki\ExternalResource\ExternalResourceUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\ExternalResource;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class ExternalResourceTest extends TestCase
{
    /**
     * When a resource is created, a SendDiscordNotification job shall be dispatched.
     */
    public function testResourceCreatedSendsDiscordNotification(): void
    {
        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(ExternalResourceCreated::class);

        ExternalResource::factory()->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a resource is deleted, a SendDiscordNotification job shall be dispatched.
     */
    public function testResourceDeletedSendsDiscordNotification(): void
    {
        $resource = ExternalResource::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(ExternalResourceDeleted::class);

        $resource->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a resource is restored, a SendDiscordNotification job shall be dispatched.
     */
    public function testResourceRestoredSendsDiscordNotification(): void
    {
        $resource = ExternalResource::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(ExternalResourceRestored::class);

        $resource->restore();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a resource is updated, a SendDiscordNotification job shall be dispatched.
     */
    public function testResourceUpdatedSendsDiscordNotification(): void
    {
        $resource = ExternalResource::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(ExternalResourceUpdated::class);

        $changes = ExternalResource::factory()->makeOne();

        $resource->fill($changes->getAttributes());
        $resource->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
