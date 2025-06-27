<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime;

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use Illuminate\Support\Arr;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class AnimeStoreTest.
 */
class AnimeStoreTest extends TestCase
{
    /**
     * The Anime Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $anime = Anime::factory()->makeOne();

        $response = $this->post(route('api.anime.store', $anime->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Anime Store Endpoint shall forbid users without the create anime permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $anime = Anime::factory()->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.anime.store', $anime->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Anime Store Endpoint shall require name, season, media_format, slug & year fields.
     *
     * @return void
     */
    public function testRequiredFields(): void
    {
        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Anime::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.anime.store'));

        $response->assertJsonValidationErrors([
            Anime::ATTRIBUTE_NAME,
            Anime::ATTRIBUTE_SEASON,
            Anime::ATTRIBUTE_MEDIA_FORMAT,
            Anime::ATTRIBUTE_SLUG,
            Anime::ATTRIBUTE_YEAR,
        ]);
    }

    /**
     * The Anime Store Endpoint shall create an anime.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $season = Arr::random(AnimeSeason::cases());
        $mediaFormat = Arr::random(AnimeMediaFormat::cases());

        $parameters = array_merge(
            Anime::factory()->raw(),
            [Anime::ATTRIBUTE_SEASON => $season->localize(), Anime::ATTRIBUTE_MEDIA_FORMAT => $mediaFormat->localize()],
        );

        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Anime::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.anime.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(Anime::class, 1);
    }
}
