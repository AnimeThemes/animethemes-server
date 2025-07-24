<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Auth;

use App\Constants\FeatureConstants;
use App\Events\Auth\User\UserCreated;
use App\Events\Auth\User\UserDeleted;
use App\Events\Auth\User\UserRestored;
use App\Events\Auth\User\UserUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Auth\User;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * When a user is created, a SendDiscordNotification job shall be dispatched.
     */
    public function testUserCreatedSendsDiscordNotification(): void
    {
        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(UserCreated::class);

        User::factory()->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a user is deleted, a SendDiscordNotification job shall be dispatched.
     */
    public function testUserDeletedSendsDiscordNotification(): void
    {
        $user = User::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(UserDeleted::class);

        $user->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a user is restored, a SendDiscordNotification job shall be dispatched.
     */
    public function testUserRestoredSendsDiscordNotification(): void
    {
        $user = User::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(UserRestored::class);

        $user->restore();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a user is updated, a SendDiscordNotification job shall be dispatched.
     */
    public function testUserUpdatedSendsDiscordNotification(): void
    {
        $user = User::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(UserUpdated::class);

        $changes = User::factory()->makeOne();

        $user->fill($changes->getAttributes());
        $user->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
