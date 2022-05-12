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
 * Class ThemeDestroyTest.
 */
class ThemeDestroyTest extends TestCase
{
    /**
     * The Theme Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        Event::fakeExcept(ThemeCreating::class);

        $theme = AnimeTheme::factory()->for(Anime::factory())->createOne();

        $response = $this->delete(route('api.animetheme.destroy', ['animetheme' => $theme]));

        $response->assertUnauthorized();
    }

    /**
     * The Theme Destroy Endpoint shall delete the theme.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        Event::fakeExcept(ThemeCreating::class);

        $theme = AnimeTheme::factory()->for(Anime::factory())->createOne();

        $user = User::factory()->createOne();

        $user->givePermissionTo('delete anime theme');

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.animetheme.destroy', ['animetheme' => $theme]));

        $response->assertOk();
        static::assertSoftDeleted($theme);
    }
}
