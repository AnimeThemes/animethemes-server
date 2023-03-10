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
 * Class ArtistMemberStoreTest.
 */
class ArtistMemberStoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Artist Member Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $artistMember = ArtistMember::factory()
            ->for(Artist::factory(), ArtistMember::RELATION_ARTIST)
            ->for(Artist::factory(), ArtistMember::RELATION_MEMBER)
            ->makeOne();

        $response = $this->post(route('api.artistmember.store', $artistMember->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Artist Member Store Endpoint shall forbid users without the create artist permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $artistMember = ArtistMember::factory()
            ->for(Artist::factory(), ArtistMember::RELATION_ARTIST)
            ->for(Artist::factory(), ArtistMember::RELATION_MEMBER)
            ->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.artistmember.store', $artistMember->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Artist Member Store Endpoint shall require artist and member fields.
     *
     * @return void
     */
    public function testRequiredFields(): void
    {
        $user = User::factory()->withPermission(CrudPermission::CREATE()->format(Artist::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.artistmember.store'));

        $response->assertJsonValidationErrors([
            ArtistMember::ATTRIBUTE_ARTIST,
            ArtistMember::ATTRIBUTE_MEMBER,
        ]);
    }

    /**
     * The Artist Member Store Endpoint shall create an artist member.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $parameters = array_merge(
            ArtistMember::factory()->raw(),
            [ArtistMember::ATTRIBUTE_ARTIST => Artist::factory()->createOne()->getKey()],
            [ArtistMember::ATTRIBUTE_MEMBER => Artist::factory()->createOne()->getKey()],
        );

        $user = User::factory()->withPermission(CrudPermission::CREATE()->format(Artist::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.artistmember.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(ArtistMember::TABLE, 1);
    }
}
