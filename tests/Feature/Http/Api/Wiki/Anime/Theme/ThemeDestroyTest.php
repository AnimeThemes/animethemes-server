<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime\Theme;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
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
        $theme = AnimeTheme::factory()->for(Anime::factory())->createOne();

        $response = $this->delete(route('api.animetheme.destroy', ['animetheme' => $theme]));

        $response->assertUnauthorized();
    }

    /**
     * The Theme Destroy Endpoint shall forbid users without the delete anime theme permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $theme = AnimeTheme::factory()->for(Anime::factory())->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.animetheme.destroy', ['animetheme' => $theme]));

        $response->assertForbidden();
    }

    /**
     * The Theme Destroy Endpoint shall forbid users from updating an anime theme that is trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        $theme = AnimeTheme::factory()
            ->trashed()
            ->for(Anime::factory())
            ->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE()->format(AnimeTheme::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.animetheme.destroy', ['animetheme' => $theme]));

        $response->assertNotFound();
    }

    /**
     * The Theme Destroy Endpoint shall delete the theme.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $theme = AnimeTheme::factory()->for(Anime::factory())->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE()->format(AnimeTheme::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.animetheme.destroy', ['animetheme' => $theme]));

        $response->assertOk();
        static::assertSoftDeleted($theme);
    }
}
