<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Admin;

use App\Constants\FeatureConstants;
use App\Events\Admin\Feature\FeatureCreated;
use App\Events\Admin\Feature\FeatureDeleted;
use App\Events\Admin\Feature\FeatureUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Admin\Feature as FeatureModel;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Tests\TestCase;

/**
 * Class FeatureTest.
 */
class FeatureTest extends TestCase
{
    /**
     * When a feature is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function test_feature_created_sends_discord_notification(): void
    {
        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(FeatureCreated::class);

        FeatureModel::factory()->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a feature is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function test_feature_deleted_sends_discord_notification(): void
    {
        $feature = FeatureModel::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(FeatureDeleted::class);

        $feature->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a feature is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function test_feature_updated_sends_discord_notification(): void
    {
        $feature = FeatureModel::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(FeatureUpdated::class);

        $feature->update([
            FeatureModel::ATTRIBUTE_VALUE => ! $feature->value,
        ]);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
