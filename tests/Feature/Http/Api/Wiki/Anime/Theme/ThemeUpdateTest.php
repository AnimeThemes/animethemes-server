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
 * Class ThemeUpdateTest.
 */
class ThemeUpdateTest extends TestCase
{
    /**
     * The Theme Update Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        Event::fakeExcept(ThemeCreating::class);

        $theme = AnimeTheme::factory()->for(Anime::factory())->createOne();

        $parameters = array_merge(
            AnimeTheme::factory()->raw(),
            [AnimeTheme::ATTRIBUTE_TYPE => ThemeType::getRandomInstance()->description],
        );

        $response = $this->put(route('api.animetheme.update', ['animetheme' => $theme] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Theme Update Endpoint shall update a theme.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        Event::fakeExcept(ThemeCreating::class);

        $theme = AnimeTheme::factory()->for(Anime::factory())->createOne();

        $parameters = array_merge(
            AnimeTheme::factory()->raw(),
            [AnimeTheme::ATTRIBUTE_TYPE => ThemeType::getRandomInstance()->description],
        );

        Sanctum::actingAs(
            User::factory()->withCurrentTeam('editor')->createOne(),
            ['theme:update']
        );

        $response = $this->put(route('api.animetheme.update', ['animetheme' => $theme] + $parameters));

        $response->assertOk();
    }
}
