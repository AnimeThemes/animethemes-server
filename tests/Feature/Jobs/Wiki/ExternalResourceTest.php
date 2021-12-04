<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Wiki;

use App\Constants\Config\FlagConstants;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\ExternalResource;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class ExternalResourceTest.
 */
class ExternalResourceTest extends TestCase
{
    /**
     * When a resource is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testResourceCreatedSendsDiscordNotification()
    {
        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        ExternalResource::factory()->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a resource is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testResourceDeletedSendsDiscordNotification()
    {
        $resource = ExternalResource::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
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
        $resource = ExternalResource::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
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
        $resource = ExternalResource::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $changes = ExternalResource::factory()->makeOne();

        $resource->fill($changes->getAttributes());
        $resource->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
