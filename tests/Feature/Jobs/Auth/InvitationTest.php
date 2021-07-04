<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Auth;

use App\Jobs\SendDiscordNotificationJob;
use App\Models\Auth\Invitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class InvitationTest.
 */
class InvitationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When an invitation is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testInvitationCreatedSendsDiscordNotification()
    {
        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        Invitation::factory()->create();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an invitation is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testInvitationDeletedSendsDiscordNotification()
    {
        $invitation = Invitation::factory()->create();

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $invitation->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an invitation is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testInvitationRestoredSendsDiscordNotification()
    {
        $invitation = Invitation::factory()->create();

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $invitation->restore();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an invitation is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testInvitationUpdatedSendsDiscordNotification()
    {
        $invitation = Invitation::factory()->create();

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $changes = Invitation::factory()->make();

        $invitation->fill($changes->getAttributes());
        $invitation->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
