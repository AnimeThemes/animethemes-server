<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime\Theme;

use App\Enums\Models\Wiki\ThemeType;
use App\Events\Wiki\Anime\Theme\ThemeCreating;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ThemeStoreTest.
 */
class ThemeStoreTest extends TestCase
{
    /**
     * The Theme Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        Event::fakeExcept(ThemeCreating::class);

        $theme = AnimeTheme::factory()->for(Anime::factory())->makeOne();

        $response = $this->post(route('api.animetheme.store', $theme->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Theme Store Endpoint shall require the anime_id & type field.
     *
     * @return void
     */
    public function testRequiredFields(): void
    {
        Event::fakeExcept(ThemeCreating::class);

        Sanctum::actingAs(
            User::factory()->withCurrentTeam('editor')->createOne(),
            ['theme:create']
        );

        $response = $this->post(route('api.animetheme.store'));

        $response->assertJsonValidationErrors([
            AnimeTheme::ATTRIBUTE_ANIME,
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
        Event::fakeExcept(ThemeCreating::class);

        $anime = Anime::factory()->createOne();

        $parameters = array_merge(
            AnimeTheme::factory()->raw(),
            [AnimeTheme::ATTRIBUTE_TYPE => ThemeType::getRandomInstance()->description],
            [AnimeTheme::ATTRIBUTE_ANIME => $anime->getKey()],
        );

        Sanctum::actingAs(
            User::factory()->withCurrentTeam('editor')->createOne(),
            ['theme:create']
        );

        $response = $this->post(route('api.animetheme.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(AnimeTheme::TABLE, 1);
    }
}
