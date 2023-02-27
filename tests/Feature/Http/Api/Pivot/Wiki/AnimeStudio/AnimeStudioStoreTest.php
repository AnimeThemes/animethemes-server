<?php

declare(strict_types=1);

namespace Http\Api\Pivot\Wiki\AnimeStudio;

use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\AnimeStudio;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class AnimeStudioStoreTest.
 */
class AnimeStudioStoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Anime Studio Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $animeStudio = AnimeStudio::factory()
            ->for(Anime::factory())
            ->for(Studio::factory())
            ->makeOne();

        $response = $this->post(route('api.animestudio.store', $animeStudio->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Anime Studio Store Endpoint shall forbid users without the create anime & create studio permissions.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $animeStudio = AnimeStudio::factory()
            ->for(Anime::factory())
            ->for(Studio::factory())
            ->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.animestudio.store', $animeStudio->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Anime Studio Store Endpoint shall require anime and studio fields.
     *
     * @return void
     */
    public function testRequiredFields(): void
    {
        $user = User::factory()->withPermissions(['create anime', 'create studio'])->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.animestudio.store'));

        $response->assertJsonValidationErrors([
            AnimeStudio::ATTRIBUTE_ANIME,
            AnimeStudio::ATTRIBUTE_STUDIO,
        ]);
    }

    /**
     * The Anime Studio Store Endpoint shall create an anime studio.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $parameters = [
            AnimeStudio::ATTRIBUTE_ANIME => Anime::factory()->createOne()->getKey(),
            AnimeStudio::ATTRIBUTE_STUDIO => Studio::factory()->createOne()->getKey(),
        ];

        $user = User::factory()->withPermissions(['create anime', 'create studio'])->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.animestudio.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(AnimeStudio::TABLE, 1);
    }
}
