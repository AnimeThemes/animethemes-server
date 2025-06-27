<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Wiki;

use App\Constants\FeatureConstants;
use App\Events\Wiki\Studio\StudioCreated;
use App\Events\Wiki\Studio\StudioDeleted;
use App\Events\Wiki\Studio\StudioRestored;
use App\Events\Wiki\Studio\StudioUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Studio;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Tests\TestCase;

/**
 * Class StudioTest.
 */
class StudioTest extends TestCase
{
    /**
     * When a studio is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function test_studio_created_sends_discord_notification(): void
    {
        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(StudioCreated::class);

        Studio::factory()->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a studio is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function test_studio_deleted_sends_discord_notification(): void
    {
        $studio = Studio::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(StudioDeleted::class);

        $studio->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a studio is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function test_studio_restored_sends_discord_notification(): void
    {
        $studio = Studio::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(StudioRestored::class);

        $studio->restore();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a studio is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function test_studio_updated_sends_discord_notification(): void
    {
        $studio = Studio::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(StudioUpdated::class);

        $changes = Studio::factory()->makeOne();

        $studio->fill($changes->getAttributes());
        $studio->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
