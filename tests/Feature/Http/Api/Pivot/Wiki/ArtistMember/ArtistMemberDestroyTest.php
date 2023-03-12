<?php

declare(strict_types=1);

namespace Http\Api\Pivot\Wiki\ArtistMember;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use App\Pivots\Wiki\ArtistMember;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ArtistMemberDestroyTest.
 */
class ArtistMemberDestroyTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Artist Member Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $artistMember = ArtistMember::factory()
            ->for(Artist::factory(), ArtistMember::RELATION_ARTIST)
            ->for(Artist::factory(), ArtistMember::RELATION_MEMBER)
            ->createOne();

        $response = $this->delete(route('api.artistmember.destroy', ['artist' => $artistMember->artist, 'member' => $artistMember->member]));

        $response->assertUnauthorized();
    }

    /**
     * The Artist Member Destroy Endpoint shall forbid users without the delete artist permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $artistMember = ArtistMember::factory()
            ->for(Artist::factory(), ArtistMember::RELATION_ARTIST)
            ->for(Artist::factory(), ArtistMember::RELATION_MEMBER)
            ->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.artistmember.destroy', ['artist' => $artistMember->artist, 'member' => $artistMember->member]));

        $response->assertForbidden();
    }

    /**
     * The Artist Member Destroy Endpoint shall return an error if the artist member does not exist.
     *
     * @return void
     */
    public function testNotFound(): void
    {
        $artist = Artist::factory()->createOne();
        $member = Artist::factory()->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE()->format(Artist::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.artistmember.destroy', ['artist' => $artist, 'member' => $member]));

        $response->assertNotFound();
    }

    /**
     * The Artist Member Destroy Endpoint shall delete the artist member.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $artistMember = ArtistMember::factory()
            ->for(Artist::factory(), ArtistMember::RELATION_ARTIST)
            ->for(Artist::factory(), ArtistMember::RELATION_MEMBER)
            ->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE()->format(Artist::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.artistmember.destroy', ['artist' => $artistMember->artist, 'member' => $artistMember->member]));

        $response->assertOk();
        static::assertModelMissing($artistMember);
    }
}
