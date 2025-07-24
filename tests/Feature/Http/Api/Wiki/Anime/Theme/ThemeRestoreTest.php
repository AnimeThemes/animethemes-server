<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime\Theme;

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ThemeRestoreTest extends TestCase
{
    /**
     * The Theme Restore Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $theme = AnimeTheme::factory()
            ->trashed()
            ->for(Anime::factory())
            ->createOne();

        $response = $this->patch(route('api.animetheme.restore', ['animetheme' => $theme]));

        $response->assertUnauthorized();
    }

    /**
     * The Theme Restore Endpoint shall forbid users without the restore anime theme permission.
     */
    public function testForbidden(): void
    {
        $theme = AnimeTheme::factory()
            ->trashed()
            ->for(Anime::factory())
            ->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.animetheme.restore', ['animetheme' => $theme]));

        $response->assertForbidden();
    }

    /**
     * The Theme Restore Endpoint shall forbid users from restoring an anime theme that isn't trashed.
     */
    public function testTrashed(): void
    {
        $theme = AnimeTheme::factory()->for(Anime::factory())->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(AnimeTheme::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.animetheme.restore', ['animetheme' => $theme]));

        $response->assertForbidden();
    }

    /**
     * The Theme Restore Endpoint shall restore the theme.
     */
    public function testRestored(): void
    {
        $theme = AnimeTheme::factory()
            ->trashed()
            ->for(Anime::factory())
            ->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(AnimeTheme::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.animetheme.restore', ['animetheme' => $theme]));

        $response->assertOk();
        static::assertNotSoftDeleted($theme);
    }
}
