<?php

declare(strict_types=1);

namespace Http\Api\Pivot\Wiki\AnimeResource;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\AnimeResource;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class AnimeResourceUpdateTest.
 */
class AnimeResourceUpdateTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Anime Resource Update Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $animeResource = AnimeResource::factory()
            ->for(Anime::factory())
            ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
            ->createOne();

        $parameters = AnimeResource::factory()->raw();

        $response = $this->put(route('api.animeresource.update', ['anime' => $animeResource->anime, 'resource' => $animeResource->resource] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Anime Resource Update Endpoint shall forbid users without the update anime & update resource permissions.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $animeResource = AnimeResource::factory()
            ->for(Anime::factory())
            ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
            ->createOne();

        $parameters = AnimeResource::factory()->raw();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.animeresource.update', ['anime' => $animeResource->anime, 'resource' => $animeResource->resource] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Anime Resource Update Endpoint shall update an anime resource.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        $animeResource = AnimeResource::factory()
            ->for(Anime::factory())
            ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
            ->createOne();

        $parameters = AnimeResource::factory()->raw();

        $user = User::factory()->withPermissions([CrudPermission::UPDATE()->format(Anime::class), CrudPermission::UPDATE()->format(ExternalResource::class)])->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.animeresource.update', ['anime' => $animeResource->anime, 'resource' => $animeResource->resource] + $parameters));

        $response->assertOk();
    }
}
