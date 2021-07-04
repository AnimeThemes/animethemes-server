<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Pivot;

use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Artist;
use App\Pivots\ArtistMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class ArtistMemberTest.
 */
class ArtistMemberTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When an Artist is attached to a Member or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testArtistMemberCreatedSendsDiscordNotification()
    {
        $artist = Artist::factory()->create();
        $member = Artist::factory()->create();

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $artist->members()->attach($member);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an Artist is detached from a Member or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testArtistMemberDeletedSendsDiscordNotification()
    {
        $artist = Artist::factory()->create();
        $member = Artist::factory()->create();
        $artist->members()->attach($member);

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $artist->members()->detach($member);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an Artist Member pivot is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testArtistMemberUpdatedSendsDiscordNotification()
    {
        $artist = Artist::factory()->create();
        $member = Artist::factory()->create();

        $artistMember = ArtistMember::factory()
            ->for($artist, 'artist')
            ->for($member, 'member')
            ->create();

        $changes = ArtistMember::factory()
            ->for($artist, 'artist')
            ->for($member, 'member')
            ->make();

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $artistMember->fill($changes->getAttributes());
        $artistMember->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
