<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Auth;

use App\Jobs\SendDiscordNotificationJob;
use App\Models\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class UserTest.
 */
class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When a user is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testUserCreatedSendsDiscordNotification()
    {
        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        User::factory()->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a user is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testUserDeletedSendsDiscordNotification()
    {
        $user = User::factory()->createOne();

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $user->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a user is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testUserRestoredSendsDiscordNotification()
    {
        $user = User::factory()->createOne();

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $user->restore();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a user is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testUserUpdatedSendsDiscordNotification()
    {
        $user = User::factory()->createOne();

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $changes = User::factory()->makeOne();

        $user->fill($changes->getAttributes());
        $user->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
