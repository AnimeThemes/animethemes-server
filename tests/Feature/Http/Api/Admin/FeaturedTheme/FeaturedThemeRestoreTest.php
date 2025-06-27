<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Admin\FeaturedTheme;

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Admin\FeaturedTheme;
use App\Models\Auth\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class FeaturedThemeRestoreTest.
 */
class FeaturedThemeRestoreTest extends TestCase
{
    /**
     * The Featured Theme Restore Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $featuredTheme = FeaturedTheme::factory()->trashed()->createOne();

        $response = $this->patch(route('api.featuredtheme.restore', ['featuredtheme' => $featuredTheme]));

        $response->assertUnauthorized();
    }

    /**
     * The Featured Theme Restore Endpoint shall forbid users without the restore featured theme permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $featuredTheme = FeaturedTheme::factory()->trashed()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.featuredtheme.restore', ['featuredtheme' => $featuredTheme]));

        $response->assertForbidden();
    }

    /**
     * The Featured Theme Restore Endpoint shall forbid users from restoring a featured theme that isn't trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        $featuredTheme = FeaturedTheme::factory()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(FeaturedTheme::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.featuredtheme.restore', ['featuredtheme' => $featuredTheme]));

        $response->assertForbidden();
    }

    /**
     * The Featured Theme Restore Endpoint shall restore the featured theme.
     *
     * @return void
     */
    public function testRestored(): void
    {
        $featuredTheme = FeaturedTheme::factory()->trashed()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(FeaturedTheme::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.featuredtheme.restore', ['featuredtheme' => $featuredTheme]));

        $response->assertOk();
        static::assertNotSoftDeleted($featuredTheme);
    }
}
