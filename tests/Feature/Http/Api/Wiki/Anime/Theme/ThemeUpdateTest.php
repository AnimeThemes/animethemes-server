<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime\Theme;

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\Wiki\ThemeType;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use Illuminate\Support\Arr;
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
        $theme = AnimeTheme::factory()->for(Anime::factory())->createOne();

        $type = Arr::random(ThemeType::cases());

        $parameters = array_merge(
            AnimeTheme::factory()->raw(),
            [AnimeTheme::ATTRIBUTE_TYPE => $type->localize()],
        );

        $response = $this->put(route('api.animetheme.update', ['animetheme' => $theme] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Theme Update Endpoint shall forbid users without the update anime theme permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $theme = AnimeTheme::factory()->for(Anime::factory())->createOne();

        $type = Arr::random(ThemeType::cases());

        $parameters = array_merge(
            AnimeTheme::factory()->raw(),
            [AnimeTheme::ATTRIBUTE_TYPE => $type->localize()],
        );

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.animetheme.update', ['animetheme' => $theme] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Theme Update Endpoint shall forbid users from updating an anime theme that is trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        $theme = AnimeTheme::factory()
            ->trashed()
            ->for(Anime::factory())
            ->createOne();

        $type = Arr::random(ThemeType::cases());

        $parameters = array_merge(
            AnimeTheme::factory()->raw(),
            [AnimeTheme::ATTRIBUTE_TYPE => $type->localize()],
        );

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(AnimeTheme::class))->createOne();

        Sanctum::actingAs($user);

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
        $theme = AnimeTheme::factory()->for(Anime::factory())->createOne();

        $type = Arr::random(ThemeType::cases());

        $parameters = array_merge(
            AnimeTheme::factory()->raw(),
            [AnimeTheme::ATTRIBUTE_TYPE => $type->localize()],
        );

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(AnimeTheme::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.animetheme.update', ['animetheme' => $theme] + $parameters));

        $response->assertOk();
    }
}
