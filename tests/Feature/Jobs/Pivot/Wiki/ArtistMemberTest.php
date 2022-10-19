<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Pivot\Wiki;

use App\Constants\Config\FlagConstants;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Artist;
use App\Pivots\Wiki\ArtistMember;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class ArtistMemberTest.
 */
class ArtistMemberTest extends TestCase
{
    /**
     * When an Artist is attached to a Member or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testArtistMemberCreatedSendsDiscordNotification(): void
    {
        $artist = Artist::factory()->createOne();
        $member = Artist::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $artist->members()->attach($member);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an Artist is detached from a Member or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testArtistMemberDeletedSendsDiscordNotification(): void
    {
        $artist = Artist::factory()->createOne();
        $member = Artist::factory()->createOne();

        $artist->members()->attach($member);

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $artist->members()->detach($member);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an Artist Member pivot is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testArtistMemberUpdatedSendsDiscordNotification(): void
    {
        $artist = Artist::factory()->createOne();
        $member = Artist::factory()->createOne();

        $artistMember = ArtistMember::factory()
            ->for($artist, 'artist')
            ->for($member, 'member')
            ->createOne();

        $changes = ArtistMember::factory()
            ->for($artist, 'artist')
            ->for($member, 'member')
            ->makeOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $artistMember->fill($changes->getAttributes());
        $artistMember->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
