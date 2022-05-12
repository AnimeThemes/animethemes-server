<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime;

use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class AnimeForceDeleteTest.
 */
class AnimeForceDeleteTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Anime Force Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $anime = Anime::factory()->createOne();

        $response = $this->delete(route('api.anime.forceDelete', ['anime' => $anime]));

        $response->assertUnauthorized();
    }

    /**
     * The Anime Force Destroy Endpoint shall force delete the anime.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $anime = Anime::factory()->createOne();

        $user = User::factory()->createOne();

        $user->givePermissionTo('force delete anime');

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.anime.forceDelete', ['anime' => $anime]));

        $response->assertOk();
        static::assertModelMissing($anime);
    }
}
