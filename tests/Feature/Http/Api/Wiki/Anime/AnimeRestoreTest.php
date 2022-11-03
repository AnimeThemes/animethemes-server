<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime;

use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class AnimeRestoreTest.
 */
class AnimeRestoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Anime Restore Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $anime = Anime::factory()->createOne();

        $anime->delete();

        $response = $this->patch(route('api.anime.restore', ['anime' => $anime]));

        $response->assertUnauthorized();
    }

    /**
     * The Anime Restore Endpoint shall forbid users without the restore anime permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $anime = Anime::factory()->createOne();

        $anime->delete();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.anime.restore', ['anime' => $anime]));

        $response->assertForbidden();
    }

    /**
     * The Anime Restore Endpoint shall forbid users from restoring an anime that isn't trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        $anime = Anime::factory()->createOne();

        $user = User::factory()->withPermission('restore anime')->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.anime.restore', ['anime' => $anime]));

        $response->assertForbidden();
    }

    /**
     * The Anime Restore Endpoint shall restore the anime.
     *
     * @return void
     */
    public function testRestored(): void
    {
        $anime = Anime::factory()->createOne();

        $anime->delete();

        $user = User::factory()->withPermission('restore anime')->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.anime.restore', ['anime' => $anime]));

        $response->assertOk();
        static::assertNotSoftDeleted($anime);
    }
}
