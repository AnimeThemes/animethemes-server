<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\List\Playlist;

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\List\PlaylistVisibility;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class PlaylistStoreTest.
 */
class PlaylistStoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Playlist Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $playlist = Playlist::factory()->makeOne();

        $response = $this->post(route('api.playlist.store', $playlist->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Playlist Store Endpoint shall forbid users without the create playlist permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $playlist = Playlist::factory()->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.store', $playlist->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Playlist Store Endpoint shall require name & visibility fields.
     *
     * @return void
     */
    public function testRequiredFields(): void
    {
        $user = User::factory()->withPermission(CrudPermission::CREATE()->format(Playlist::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.store'));

        $response->assertJsonValidationErrors([
            Playlist::ATTRIBUTE_NAME,
            Playlist::ATTRIBUTE_VISIBILITY,
        ]);
    }

    /**
     * The Playlist Store Endpoint shall create a playlist.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $parameters = array_merge(
            Playlist::factory()->raw(),
            [Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::getRandomInstance()->description],
        );

        $user = User::factory()->withPermission(CrudPermission::CREATE()->format(Playlist::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(Playlist::TABLE, 1);
        static::assertDatabaseHas(Playlist::TABLE, [Playlist::ATTRIBUTE_USER => $user->getKey()]);
    }
}
