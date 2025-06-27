<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Pivot\Wiki\SongResource;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Song;
use App\Pivots\Wiki\SongResource;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class SongResourceStoreTest.
 */
class SongResourceStoreTest extends TestCase
{
    /**
     * The Song Resource Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function test_protected(): void
    {
        $song = Song::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $parameters = SongResource::factory()->raw();

        $response = $this->post(route('api.songresource.store', ['song' => $song, 'resource' => $resource] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Song Resource Store Endpoint shall forbid users without the create song & create resource permissions.
     *
     * @return void
     */
    public function test_forbidden(): void
    {
        $song = Song::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $parameters = SongResource::factory()->raw();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.songresource.store', ['song' => $song, 'resource' => $resource] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Song Resource Store Endpoint shall create an song resource.
     *
     * @return void
     */
    public function test_create(): void
    {
        $song = Song::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $parameters = SongResource::factory()->raw();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::CREATE->format(Song::class),
                CrudPermission::CREATE->format(ExternalResource::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.songresource.store', ['song' => $song, 'resource' => $resource] + $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(SongResource::class, 1);
    }
}
