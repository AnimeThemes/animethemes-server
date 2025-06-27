<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Admin\FeaturedTheme;

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Admin\FeaturedTheme;
use App\Models\Auth\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class FeaturedThemeForceDeleteTest.
 */
class FeaturedThemeForceDeleteTest extends TestCase
{
    /**
     * The Featured Theme Force Delete Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function test_protected(): void
    {
        $featuredTheme = FeaturedTheme::factory()->createOne();

        $response = $this->delete(route('api.featuredtheme.forceDelete', ['featuredtheme' => $featuredTheme]));

        $response->assertUnauthorized();
    }

    /**
     * The Featured Theme Force Delete Endpoint shall forbid users without the force delete featured theme permission.
     *
     * @return void
     */
    public function test_forbidden(): void
    {
        $featuredTheme = FeaturedTheme::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.featuredtheme.forceDelete', ['featuredtheme' => $featuredTheme]));

        $response->assertForbidden();
    }

    /**
     * The Featured Theme Force Delete Endpoint shall force delete the featured theme.
     *
     * @return void
     */
    public function test_deleted(): void
    {
        $featuredTheme = FeaturedTheme::factory()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE->format(FeaturedTheme::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.featuredtheme.forceDelete', ['featuredtheme' => $featuredTheme]));

        $response->assertOk();
        static::assertModelMissing($featuredTheme);
    }
}
