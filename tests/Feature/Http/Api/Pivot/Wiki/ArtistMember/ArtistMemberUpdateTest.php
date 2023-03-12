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
 * Class ArtistMemberUpdateTest.
 */
class ArtistMemberUpdateTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Artist Member Update Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $artistMember = ArtistMember::factory()
            ->for(Artist::factory(), ArtistMember::RELATION_ARTIST)
            ->for(Artist::factory(), ArtistMember::RELATION_MEMBER)
            ->createOne();

        $parameters = ArtistMember::factory()->raw();

        $response = $this->put(route('api.artistmember.update', ['artist' => $artistMember->artist, 'member' => $artistMember->member] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Artist Member Update Endpoint shall forbid users without the update artist permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $artistMember = ArtistMember::factory()
            ->for(Artist::factory(), ArtistMember::RELATION_ARTIST)
            ->for(Artist::factory(), ArtistMember::RELATION_MEMBER)
            ->createOne();

        $parameters = ArtistMember::factory()->raw();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.artistmember.update', ['artist' => $artistMember->artist, 'member' => $artistMember->member] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Artist Member Update Endpoint shall update an artist member.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        $artistMember = ArtistMember::factory()
            ->for(Artist::factory(), ArtistMember::RELATION_ARTIST)
            ->for(Artist::factory(), ArtistMember::RELATION_MEMBER)
            ->createOne();

        $parameters = ArtistMember::factory()->raw();

        $user = User::factory()->withPermissions(CrudPermission::UPDATE()->format(Artist::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.artistmember.update', ['artist' => $artistMember->artist, 'member' => $artistMember->member] + $parameters));

        $response->assertOk();
    }
}
