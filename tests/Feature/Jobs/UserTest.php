<?php

namespace Tests\Feature\Jobs;

use App\Jobs\SendDiscordNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

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
        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        User::factory()->create();

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When a user is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testUserDeletedSendsDiscordNotification()
    {
        $user = User::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $user->delete();

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When a user is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testUserRestoredSendsDiscordNotification()
    {
        $user = User::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $user->restore();

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When a user is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testUserUpdatedSendsDiscordNotification()
    {
        $user = User::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $changes = User::factory()->make();

        $user->fill($changes->getAttributes());
        $user->save();

        Bus::assertDispatched(SendDiscordNotification::class);
    }
}
