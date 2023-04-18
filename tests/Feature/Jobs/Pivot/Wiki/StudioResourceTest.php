<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Pivot\Wiki;

use App\Constants\Config\FlagConstants;
use App\Events\Pivot\Wiki\StudioResource\StudioResourceCreated;
use App\Events\Pivot\Wiki\StudioResource\StudioResourceDeleted;
use App\Events\Pivot\Wiki\StudioResource\StudioResourceUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\StudioResource;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

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
    public function testStudioResourceCreatedSendsDiscordNotification(): void
    {
        $studio = Studio::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(StudioResourceCreated::class);

        $studio->resources()->attach($resource);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a Studio is detached from a Resource or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testStudioResourceDeletedSendsDiscordNotification(): void
    {
        $studio = Studio::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $studio->resources()->attach($resource);

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(StudioResourceDeleted::class);

        $studio->resources()->detach($resource);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a Studio Resource pivot is updated, a SendDiscordNotification job will be dispatched.
     *
     * @return void
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

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(StudioResourceUpdated::class);

        $studioResource->fill($changes->getAttributes());
        $studioResource->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
