<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime\Theme;

use App\Events\Wiki\Anime\Theme\ThemeCreating;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ThemeForceDeleteTest.
 */
class ThemeForceDeleteTest extends TestCase
{
    /**
     * The Theme Force Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        Event::fakeExcept(ThemeCreating::class);

        $theme = AnimeTheme::factory()->for(Anime::factory())->createOne();

        $response = $this->delete(route('api.animetheme.forceDelete', ['animetheme' => $theme]));

        $response->assertUnauthorized();
    }

    /**
     * The Theme Force Destroy Endpoint shall force delete the theme.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        Event::fakeExcept(ThemeCreating::class);

        $theme = AnimeTheme::factory()->for(Anime::factory())->createOne();

        Sanctum::actingAs(
            User::factory()->withCurrentTeam('admin')->createOne(),
            ['*']
        );

        $response = $this->delete(route('api.animetheme.forceDelete', ['animetheme' => $theme]));

        $response->assertOk();
        static::assertModelMissing($theme);
    }
}
