<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime\Theme;

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\Wiki\ThemeType;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ThemeStoreTest.
 */
class ThemeStoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Theme Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $theme = AnimeTheme::factory()->for(Anime::factory())->makeOne();

        $response = $this->post(route('api.animetheme.store', $theme->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Theme Store Endpoint shall forbid users without the store anime theme permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $theme = AnimeTheme::factory()->for(Anime::factory())->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.animetheme.store', $theme->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Theme Store Endpoint shall require the anime_id & type field.
     *
     * @return void
     */
    public function testRequiredFields(): void
    {
        $user = User::factory()->withPermission(CrudPermission::CREATE()->format(AnimeTheme::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.animetheme.store'));

        $response->assertJsonValidationErrors([
            AnimeTheme::ATTRIBUTE_ANIME,
            AnimeTheme::ATTRIBUTE_SLUG,
            AnimeTheme::ATTRIBUTE_TYPE,
        ]);
    }

    /**
     * The Theme Store Endpoint shall create a theme.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $anime = Anime::factory()->createOne();

        $parameters = array_merge(
            AnimeTheme::factory()->raw(),
            [AnimeTheme::ATTRIBUTE_TYPE => ThemeType::getRandomInstance()->description],
            [AnimeTheme::ATTRIBUTE_ANIME => $anime->getKey()],
        );

        $user = User::factory()->withPermission(CrudPermission::CREATE()->format(AnimeTheme::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.animetheme.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(AnimeTheme::TABLE, 1);
    }
}
