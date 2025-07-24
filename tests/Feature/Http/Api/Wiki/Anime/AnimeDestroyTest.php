<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AnimeDestroyTest extends TestCase
{
    /**
     * The Anime Destroy Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $anime = Anime::factory()->createOne();

        $response = $this->delete(route('api.anime.destroy', ['anime' => $anime]));

        $response->assertUnauthorized();
    }

    /**
     * The Anime Destroy Endpoint shall forbid users without the delete anime permission.
     */
    public function testForbidden(): void
    {
        $anime = Anime::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.anime.destroy', ['anime' => $anime]));

        $response->assertForbidden();
    }

    /**
     * The Anime Destroy Endpoint shall forbid users from updating an anime that is trashed.
     */
    public function testTrashed(): void
    {
        $anime = Anime::factory()->trashed()->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Anime::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.anime.destroy', ['anime' => $anime]));

        $response->assertNotFound();
    }

    /**
     * The Anime Destroy Endpoint shall delete the anime.
     */
    public function testDeleted(): void
    {
        $anime = Anime::factory()->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Anime::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.anime.destroy', ['anime' => $anime]));

        $response->assertOk();
        static::assertSoftDeleted($anime);
    }
}
