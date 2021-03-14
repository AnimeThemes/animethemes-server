<?php

namespace Tests\Feature\Jobs;

use App\Jobs\SendDiscordNotification;
use App\Models\Invitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class InvitationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * When an invitation is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testInvitationCreatedSendsDiscordNotification()
    {
        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        Invitation::factory()->create();

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When an invitation is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testInvitationDeletedSendsDiscordNotification()
    {
        $invitation = Invitation::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $invitation->delete();

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When an invitation is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testInvitationRestoredSendsDiscordNotification()
    {
        $invitation = Invitation::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $invitation->restore();

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When an invitation is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testInvitationUpdatedSendsDiscordNotification()
    {
        $invitation = Invitation::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $changes = Invitation::factory()->make();

        $invitation->fill($changes->getAttributes());
        $invitation->save();

        Bus::assertDispatched(SendDiscordNotification::class);
    }
}
