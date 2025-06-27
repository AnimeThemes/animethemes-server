<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Wiki;

use App\Constants\FeatureConstants;
use App\Events\Wiki\Group\GroupCreated;
use App\Events\Wiki\Group\GroupDeleted;
use App\Events\Wiki\Group\GroupRestored;
use App\Events\Wiki\Group\GroupUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Group;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Tests\TestCase;

/**
 * Class GroupTest.
 */
class GroupTest extends TestCase
{
    /**
     * When a group is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function test_group_created_sends_discord_notification(): void
    {
        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(GroupCreated::class);

        Group::factory()->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a group is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function test_group_deleted_sends_discord_notification(): void
    {
        $group = Group::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(GroupDeleted::class);

        $group->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a group is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function test_group_restored_sends_discord_notification(): void
    {
        $group = Group::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(GroupRestored::class);

        $group->restore();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a group is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function test_group_updated_sends_discord_notification(): void
    {
        $group = Group::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(GroupUpdated::class);

        $changes = Group::factory()->makeOne();

        $group->fill($changes->getAttributes());
        $group->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
