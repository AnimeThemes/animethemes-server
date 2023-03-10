<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Artist;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ArtistStoreTest.
 */
class ArtistStoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Artist Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $artist = Artist::factory()->makeOne();

        $response = $this->post(route('api.artist.store', $artist->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Artist Store Endpoint shall forbid users without the create artist permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $artist = Artist::factory()->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.artist.store', $artist->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Artist Store Endpoint shall require name & slug fields.
     *
     * @return void
     */
    public function testRequiredFields(): void
    {
        $user = User::factory()->withPermission(CrudPermission::CREATE()->format(Artist::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.artist.store'));

        $response->assertJsonValidationErrors([
            Artist::ATTRIBUTE_NAME,
            Artist::ATTRIBUTE_SLUG,
        ]);
    }

    /**
     * The Artist Store Endpoint shall create an artist.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $parameters = Artist::factory()->raw();

        $user = User::factory()->withPermission(CrudPermission::CREATE()->format(Artist::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.artist.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(Artist::TABLE, 1);
    }
}
