<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Pivot\Wiki;

use App\Constants\FeatureConstants;
use App\Events\Pivot\Wiki\ArtistMember\ArtistMemberCreated;
use App\Events\Pivot\Wiki\ArtistMember\ArtistMemberDeleted;
use App\Events\Pivot\Wiki\ArtistMember\ArtistMemberUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Artist;
use App\Pivots\Wiki\ArtistMember;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
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

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(ArtistMemberCreated::class);

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

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(ArtistMemberDeleted::class);

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

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(ArtistMemberUpdated::class);

        $artistMember->fill($changes->getAttributes());
        $artistMember->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
