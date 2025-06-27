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
 * Class SongResourceUpdateTest.
 */
class SongResourceUpdateTest extends TestCase
{
    /**
     * The Song Resource Update Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $songResource = SongResource::factory()
            ->for(Song::factory())
            ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
            ->createOne();

        $parameters = SongResource::factory()->raw();

        $response = $this->put(route('api.songresource.update', ['song' => $songResource->song, 'resource' => $songResource->resource] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Song Resource Update Endpoint shall forbid users without the update song & update resource permissions.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $songResource = SongResource::factory()
            ->for(Song::factory())
            ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
            ->createOne();

        $parameters = SongResource::factory()->raw();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.songresource.update', ['song' => $songResource->song, 'resource' => $songResource->resource] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Song Resource Update Endpoint shall update an song resource.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        $songResource = SongResource::factory()
            ->for(Song::factory())
            ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
            ->createOne();

        $parameters = SongResource::factory()->raw();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::UPDATE->format(Song::class),
                CrudPermission::UPDATE->format(ExternalResource::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.songresource.update', ['song' => $songResource->song, 'resource' => $songResource->resource] + $parameters));

        $response->assertOk();
    }
}
