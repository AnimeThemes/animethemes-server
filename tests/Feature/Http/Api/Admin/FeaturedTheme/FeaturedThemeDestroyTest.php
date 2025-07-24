<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Admin\FeaturedTheme;

use App\Enums\Auth\CrudPermission;
use App\Models\Admin\FeaturedTheme;
use App\Models\Auth\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FeaturedThemeDestroyTest extends TestCase
{
    /**
     * The Featured Theme Destroy Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $featuredTheme = FeaturedTheme::factory()->createOne();

        $response = $this->delete(route('api.featuredtheme.destroy', ['featuredtheme' => $featuredTheme]));

        $response->assertUnauthorized();
    }

    /**
     * The Featured Theme Destroy Endpoint shall forbid users without the delete featured theme permission.
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
     * The FeaturedTheme Destroy Endpoint shall delete the featured theme.
     */
    public function testDeleted(): void
    {
        $featuredTheme = FeaturedTheme::factory()->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(FeaturedTheme::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.featuredtheme.destroy', ['featuredtheme' => $featuredTheme]));

        $response->assertOk();
        static::assertModelMissing($featuredTheme);
    }
}
