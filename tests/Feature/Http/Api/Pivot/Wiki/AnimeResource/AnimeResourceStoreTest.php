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
 * Class AnimeResourceStoreTest.
 */
class AnimeResourceStoreTest extends TestCase
{
    /**
     * The Anime Resource Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function test_protected(): void
    {
        $anime = Anime::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $parameters = AnimeResource::factory()->raw();

        $response = $this->post(route('api.animeresource.store', ['anime' => $anime, 'resource' => $resource] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Anime Resource Store Endpoint shall forbid users without the create anime & create resource permissions.
     *
     * @return void
     */
    public function test_forbidden(): void
    {
        $anime = Anime::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $parameters = AnimeResource::factory()->raw();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.animeresource.store', ['anime' => $anime, 'resource' => $resource] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Anime Resource Store Endpoint shall create an anime resource.
     *
     * @return void
     */
    public function test_create(): void
    {
        $anime = Anime::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $parameters = AnimeResource::factory()->raw();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::CREATE->format(Anime::class),
                CrudPermission::CREATE->format(ExternalResource::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.animeresource.store', ['anime' => $anime, 'resource' => $resource] + $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(AnimeResource::class, 1);
    }
}
