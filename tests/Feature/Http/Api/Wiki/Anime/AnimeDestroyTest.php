<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime;

use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class AnimeDestroyTest.
 */
class AnimeDestroyTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Anime Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $anime = Anime::factory()->createOne();

        $response = $this->delete(route('api.anime.destroy', ['anime' => $anime]));

        $response->assertUnauthorized();
    }

    /**
     * The Anime Destroy Endpoint shall delete the anime.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $anime = Anime::factory()->createOne();

        Sanctum::actingAs(
            User::factory()->withCurrentTeam('editor')->createOne(),
            ['anime:delete']
        );

        $response = $this->delete(route('api.anime.destroy', ['anime' => $anime]));

        $response->assertOk();
        static::assertSoftDeleted($anime);
    }
}
