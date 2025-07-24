<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\List;

use App\Constants\FeatureConstants;
use App\Events\List\ExternalProfile\ExternalProfileCreated;
use App\Events\List\ExternalProfile\ExternalProfileDeleted;
use App\Events\List\ExternalProfile\ExternalProfileUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\List\ExternalProfile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class ExternalProfileTest extends TestCase
{
    /**
     * When a profile is created, a SendDiscordNotification job shall be dispatched.
     */
    public function testExternalProfileCreatedSendsDiscordNotification(): void
    {
        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(ExternalProfileCreated::class);

        ExternalProfile::factory()->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a profile is deleted, a SendDiscordNotification job shall not be dispatched.
     */
    public function testExternalProfileDeletedSendsDiscordNotification(): void
    {
        $profile = ExternalProfile::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(ExternalProfileDeleted::class);

        $profile->delete();

        Bus::assertNotDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a profile is updated, a SendDiscordNotification job shall not be dispatched.
     */
    public function testExternalProfileUpdatedSendsDiscordNotification(): void
    {
        $profile = ExternalProfile::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(ExternalProfileUpdated::class);

        $changes = ExternalProfile::factory()->makeOne();

        $profile->fill($changes->getAttributes());
        $profile->save();

        Bus::assertNotDispatched(SendDiscordNotificationJob::class);
    }
}
