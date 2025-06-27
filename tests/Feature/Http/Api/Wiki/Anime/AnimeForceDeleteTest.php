<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime;

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class AnimeForceDeleteTest.
 */
class AnimeForceDeleteTest extends TestCase
{
    /**
     * The Anime Force Delete Endpoint shall require authorization.
     *
     * @return void
     */
    public function test_authorized(): void
    {
        $anime = Anime::factory()->createOne();

        $response = $this->delete(route('api.anime.forceDelete', ['anime' => $anime]));

        $response->assertUnauthorized();
    }

    /**
     * The Anime Force Delete Endpoint shall forbid users without the force delete anime permission.
     *
     * @return void
     */
    public function test_forbidden(): void
    {
        $anime = Anime::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.anime.forceDelete', ['anime' => $anime]));

        $response->assertForbidden();
    }

    /**
     * The Anime Force Delete Endpoint shall force delete the anime.
     *
     * @return void
     */
    public function test_deleted(): void
    {
        $anime = Anime::factory()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE->format(Anime::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.anime.forceDelete', ['anime' => $anime]));

        $response->assertOk();
        static::assertModelMissing($anime);
    }
}
