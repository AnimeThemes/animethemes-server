<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Pivot\Wiki\AnimeResource;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\AnimeResource;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class AnimeResourceDestroyTest.
 */
class AnimeResourceDestroyTest extends TestCase
{
    /**
     * The Anime Resource Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function test_protected(): void
    {
        $animeResource = AnimeResource::factory()
            ->for(Anime::factory())
            ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
            ->createOne();

        $response = $this->delete(route('api.animeresource.destroy', ['anime' => $animeResource->anime, 'resource' => $animeResource->resource]));

        $response->assertUnauthorized();
    }

    /**
     * The Anime Resource Destroy Endpoint shall forbid users without the delete anime & delete resource permissions.
     *
     * @return void
     */
    public function test_forbidden(): void
    {
        $animeResource = AnimeResource::factory()
            ->for(Anime::factory())
            ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
            ->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.animeresource.destroy', ['anime' => $animeResource->anime, 'resource' => $animeResource->resource]));

        $response->assertForbidden();
    }

    /**
     * The Anime Resource Destroy Endpoint shall return an error if the anime resource does not exist.
     *
     * @return void
     */
    public function test_not_found(): void
    {
        $anime = Anime::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::DELETE->format(Anime::class),
                CrudPermission::DELETE->format(ExternalResource::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.animeresource.destroy', ['anime' => $anime, 'resource' => $resource]));

        $response->assertNotFound();
    }

    /**
     * The Anime Resource Destroy Endpoint shall delete the anime resource.
     *
     * @return void
     */
    public function test_deleted(): void
    {
        $animeResource = AnimeResource::factory()
            ->for(Anime::factory())
            ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
            ->createOne();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::DELETE->format(Anime::class),
                CrudPermission::DELETE->format(ExternalResource::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.animeresource.destroy', ['anime' => $animeResource->anime, 'resource' => $animeResource->resource]));

        $response->assertOk();
        static::assertModelMissing($animeResource);
    }
}
