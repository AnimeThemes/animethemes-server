<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Pivot\Wiki\SongResource;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Song;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\SongResource;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class SongResourceDestroyTest.
 */
class SongResourceDestroyTest extends TestCase
{
    /**
     * The Song Resource Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $songResource = SongResource::factory()
            ->for(Song::factory())
            ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
            ->createOne();

        $response = $this->delete(route('api.songresource.destroy', ['song' => $songResource->song, 'resource' => $songResource->resource]));

        $response->assertUnauthorized();
    }

    /**
     * The Song Resource Destroy Endpoint shall forbid users without the delete song & delete resource permissions.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $songResource = SongResource::factory()
            ->for(Song::factory())
            ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
            ->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.songresource.destroy', ['song' => $songResource->song, 'resource' => $songResource->resource]));

        $response->assertForbidden();
    }

    /**
     * The Song Resource Destroy Endpoint shall return an error if the song resource does not exist.
     *
     * @return void
     */
    public function testNotFound(): void
    {
        $song = Song::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::DELETE->format(Song::class),
                CrudPermission::DELETE->format(ExternalResource::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.songresource.destroy', ['song' => $song, 'resource' => $resource]));

        $response->assertNotFound();
    }

    /**
     * The Song Resource Destroy Endpoint shall delete the song resource.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $songResource = SongResource::factory()
            ->for(Song::factory())
            ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
            ->createOne();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::DELETE->format(Song::class),
                CrudPermission::DELETE->format(ExternalResource::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.songresource.destroy', ['song' => $songResource->song, 'resource' => $songResource->resource]));

        $response->assertOk();
        static::assertModelMissing($songResource);
    }
}
