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
 * Class ThemeRestoreTest.
 */
class ThemeRestoreTest extends TestCase
{
    /**
     * The Theme Restore Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        Event::fakeExcept(ThemeCreating::class);

        $theme = AnimeTheme::factory()->for(Anime::factory())->createOne();

        $theme->delete();

        $response = $this->patch(route('api.animetheme.restore', ['animetheme' => $theme]));

        $response->assertUnauthorized();
    }

    /**
     * The Theme Restore Endpoint shall restore the theme.
     *
     * @return void
     */
    public function testRestored(): void
    {
        Event::fakeExcept(ThemeCreating::class);

        $theme = AnimeTheme::factory()->for(Anime::factory())->createOne();

        $theme->delete();

        $user = User::factory()->withPermission('restore anime theme')->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.animetheme.restore', ['animetheme' => $theme]));

        $response->assertOk();
        static::assertNotSoftDeleted($theme);
    }
}
