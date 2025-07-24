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

class AnimeStudioStoreTest extends TestCase
{
    /**
     * The Anime Studio Store Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $anime = Anime::factory()->createOne();
        $studio = Studio::factory()->createOne();

        $response = $this->post(route('api.animestudio.store', ['anime' => $anime, 'studio' => $studio]));

        $response->assertUnauthorized();
    }

    /**
     * The Anime Studio Store Endpoint shall forbid users without the create anime & create studio permissions.
     */
    public function testForbidden(): void
    {
        $anime = Anime::factory()->createOne();
        $studio = Studio::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.animestudio.store', ['anime' => $anime, 'studio' => $studio]));

        $response->assertForbidden();
    }

    /**
     * The Anime Studio Store Endpoint shall create an anime studio.
     */
    public function testCreate(): void
    {
        $anime = Anime::factory()->createOne();
        $studio = Studio::factory()->createOne();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::CREATE->format(Anime::class),
                CrudPermission::CREATE->format(Studio::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.animestudio.store', ['anime' => $anime, 'studio' => $studio]));

        $response->assertCreated();
        static::assertDatabaseCount(AnimeStudio::class, 1);
    }
}
