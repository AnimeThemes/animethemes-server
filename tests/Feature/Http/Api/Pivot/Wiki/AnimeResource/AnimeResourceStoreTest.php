<?php

declare(strict_types=1);

namespace Http\Api\Pivot\Wiki\AnimeResource;

use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\AnimeResource;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class AnimeResourceStoreTest.
 */
class AnimeResourceStoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Anime Resource Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $animeResource = AnimeResource::factory()
            ->for(Anime::factory())
            ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
            ->makeOne();

        $response = $this->post(route('api.animeresource.store', $animeResource->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Anime Resource Store Endpoint shall forbid users without the create anime & create resource permissions.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $animeResource = AnimeResource::factory()
            ->for(Anime::factory())
            ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
            ->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.animeresource.store', $animeResource->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Anime Resource Store Endpoint shall require anime and resource fields.
     *
     * @return void
     */
    public function testRequiredFields(): void
    {
        $user = User::factory()->withPermissions(['create anime', 'create external resource'])->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.animeresource.store'));

        $response->assertJsonValidationErrors([
            AnimeResource::ATTRIBUTE_ANIME,
            AnimeResource::ATTRIBUTE_RESOURCE,
        ]);
    }

    /**
     * The Anime Resource Store Endpoint shall create an anime resource.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $parameters = array_merge(
            AnimeResource::factory()->raw(),
            [AnimeResource::ATTRIBUTE_ANIME => Anime::factory()->createOne()->getKey()],
            [AnimeResource::ATTRIBUTE_RESOURCE => ExternalResource::factory()->createOne()->getKey()],
        );

        $user = User::factory()->withPermissions(['create anime', 'create external resource'])->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.animeresource.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(AnimeResource::TABLE, 1);
    }
}
