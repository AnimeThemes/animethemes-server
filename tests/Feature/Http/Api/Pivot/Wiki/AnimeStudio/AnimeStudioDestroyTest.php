<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Pivot\Wiki\AnimeStudio;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\AnimeStudio;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class AnimeStudioDestroyTest.
 */
class AnimeStudioDestroyTest extends TestCase
{
    /**
     * The Anime Studio Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function test_protected(): void
    {
        $animeStudio = AnimeStudio::factory()
            ->for(Anime::factory())
            ->for(Studio::factory())
            ->createOne();

        $response = $this->delete(route('api.animestudio.destroy', ['anime' => $animeStudio->anime, 'studio' => $animeStudio->studio]));

        $response->assertUnauthorized();
    }

    /**
     * The Anime Studio Destroy Endpoint shall forbid users without the delete anime & delete studio permissions.
     *
     * @return void
     */
    public function test_forbidden(): void
    {
        $animeStudio = AnimeStudio::factory()
            ->for(Anime::factory())
            ->for(Studio::factory())
            ->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.animestudio.destroy', ['anime' => $animeStudio->anime, 'studio' => $animeStudio->studio]));

        $response->assertForbidden();
    }

    /**
     * The Anime Studio Destroy Endpoint shall return an error if the anime studio does not exist.
     *
     * @return void
     */
    public function test_not_found(): void
    {
        $anime = Anime::factory()->createOne();
        $studio = Studio::factory()->createOne();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::DELETE->format(Anime::class),
                CrudPermission::DELETE->format(Studio::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.animestudio.destroy', ['anime' => $anime, 'studio' => $studio]));

        $response->assertNotFound();
    }

    /**
     * The Anime Studio Destroy Endpoint shall delete the anime studio.
     *
     * @return void
     */
    public function test_deleted(): void
    {
        $animeStudio = AnimeStudio::factory()
            ->for(Anime::factory())
            ->for(Studio::factory())
            ->createOne();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::DELETE->format(Anime::class),
                CrudPermission::DELETE->format(Studio::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.animestudio.destroy', ['anime' => $animeStudio->anime, 'studio' => $animeStudio->studio]));

        $response->assertOk();
        static::assertModelMissing($animeStudio);
    }
}
