<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Pivot\Wiki\ArtistMember;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use App\Pivots\Wiki\ArtistMember;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ArtistMemberStoreTest.
 */
class ArtistMemberStoreTest extends TestCase
{
    /**
     * The Artist Member Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $artist = Artist::factory()->createOne();
        $member = Artist::factory()->createOne();

        $parameters = ArtistMember::factory()->raw();

        $response = $this->post(route('api.artistmember.store', ['artist' => $artist, 'member' => $member] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Artist Member Store Endpoint shall forbid users without the create artist permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $artist = Artist::factory()->createOne();
        $member = Artist::factory()->createOne();

        $parameters = ArtistMember::factory()->raw();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.artistmember.store', ['artist' => $artist, 'member' => $member] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Artist Member Store Endpoint shall create an artist member.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $artist = Artist::factory()->createOne();
        $member = Artist::factory()->createOne();

        $parameters = ArtistMember::factory()->raw();

        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Artist::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.artistmember.store', ['artist' => $artist, 'member' => $member] + $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(ArtistMember::class, 1);
    }

    /**
     * The Artist Member Store Endpoint shall prohibit artist_id & member_id being the same.
     *
     * @return void
     */
    public function testArtistNotMemberOfThemselves(): void
    {
        $artist = Artist::factory()->createOne();

        $parameters = ArtistMember::factory()->raw();

        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Artist::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.artistmember.store', ['artist' => $artist, 'member' => $artist] + $parameters));

        $response->assertJsonValidationErrors([
            ArtistMember::RELATION_ARTIST,
            ArtistMember::RELATION_MEMBER,
        ]);
    }
}
