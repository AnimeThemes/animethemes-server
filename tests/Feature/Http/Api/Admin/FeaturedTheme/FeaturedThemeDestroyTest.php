<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Admin\FeaturedTheme;

use App\Enums\Auth\CrudPermission;
use App\Models\Admin\FeaturedTheme;
use App\Models\Auth\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class FeaturedThemeDestroyTest.
 */
class FeaturedThemeDestroyTest extends TestCase
{
    /**
     * The Featured Theme Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $featuredTheme = FeaturedTheme::factory()->createOne();

        $response = $this->delete(route('api.featuredtheme.destroy', ['featuredtheme' => $featuredTheme]));

        $response->assertUnauthorized();
    }

    /**
     * The Featured Theme Destroy Endpoint shall forbid users without the delete featured theme permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $featuredTheme = FeaturedTheme::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.featuredtheme.destroy', ['featuredtheme' => $featuredTheme]));

        $response->assertForbidden();
    }

    /**
     * The Featured Theme Destroy Endpoint shall forbid users from updating a featured theme that is trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        $featuredTheme = FeaturedTheme::factory()->trashed()->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(FeaturedTheme::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.featuredtheme.destroy', ['featuredtheme' => $featuredTheme]));

        $response->assertNotFound();
    }

    /**
     * The FeaturedTheme Destroy Endpoint shall delete the featured theme.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $featuredTheme = FeaturedTheme::factory()->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(FeaturedTheme::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.featuredtheme.destroy', ['featuredtheme' => $featuredTheme]));

        $response->assertOk();
        static::assertSoftDeleted($featuredTheme);
    }
}
