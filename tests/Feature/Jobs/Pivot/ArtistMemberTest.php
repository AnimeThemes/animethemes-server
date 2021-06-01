<?php declare(strict_types=1);

namespace Jobs\Pivot;

use App\Jobs\SendDiscordNotification;
use App\Models\Artist;
use App\Pivots\ArtistMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class ArtistMemberTest
 * @package Jobs\Pivot
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

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $artist->members()->attach($member);

        Bus::assertDispatched(SendDiscordNotification::class);
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

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $artist->members()->detach($member);

        Bus::assertDispatched(SendDiscordNotification::class);
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

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $artistMember->fill($changes->getAttributes());
        $artistMember->save();

        Bus::assertDispatched(SendDiscordNotification::class);
    }
}
