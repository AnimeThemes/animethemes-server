<?php

declare(strict_types=1);

namespace Jobs\Wiki;

use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\ExternalResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class ExternalResourceTest.
 */
class ExternalResourceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When a resource is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testResourceCreatedSendsDiscordNotification()
    {
        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        ExternalResource::factory()->create();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a resource is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testResourceDeletedSendsDiscordNotification()
    {
        $resource = ExternalResource::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $resource->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a resource is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testResourceRestoredSendsDiscordNotification()
    {
        $resource = ExternalResource::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $resource->restore();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a resource is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testResourceUpdatedSendsDiscordNotification()
    {
        $resource = ExternalResource::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $changes = ExternalResource::factory()->make();

        $resource->fill($changes->getAttributes());
        $resource->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
