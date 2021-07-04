<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Pivot;

use App\Events\Pivot\ArtistMember\ArtistMemberCreated;
use App\Events\Pivot\ArtistMember\ArtistMemberDeleted;
use App\Events\Pivot\ArtistMember\ArtistMemberUpdated;
use App\Models\Wiki\Artist;
use App\Pivots\ArtistMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class ArtistMemberTest.
 */
class ArtistMemberTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When an Artist is attached to a Member or vice versa, an ArtistMemberCreated event shall be dispatched.
     *
     * @return void
     */
    public function testArtistMemberCreatedEventDispatched()
    {
        Event::fake();

        $artist = Artist::factory()->create();
        $member = Artist::factory()->create();

        $artist->members()->attach($member);

        Event::assertDispatched(ArtistMemberCreated::class);
    }

    /**
     * When an Artist is detached from a Member or vice versa, an ArtistMemberDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testArtistMemberDeletedEventDispatched()
    {
        Event::fake();

        $artist = Artist::factory()->create();
        $member = Artist::factory()->create();

        $artist->members()->attach($member);
        $artist->members()->detach($member);

        Event::assertDispatched(ArtistMemberDeleted::class);
    }

    /**
     * When an Artist Member pivot is updated, an ArtistMemberUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testArtistMemberUpdatedEventDispatched()
    {
        Event::fake();

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

        $artistMember->fill($changes->getAttributes());
        $artistMember->save();

        Event::assertDispatched(ArtistMemberUpdated::class);
    }
}
