<?php

declare(strict_types=1);

namespace Http\Api\Pivot\Wiki\ArtistMember;

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

        $user = User::factory()->withPermissions(CrudPermission::CREATE()->format(Artist::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.artistmember.store', ['artist' => $artist, 'member' => $member] + $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(ArtistMember::class, 1);
    }
}
