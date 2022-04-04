<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class AnimeUpdateTest.
 */
class AnimeUpdateTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Anime Update Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $anime = Anime::factory()->createOne();

        $parameters = array_merge(
            Anime::factory()->raw(),
            [Anime::ATTRIBUTE_SEASON => AnimeSeason::getRandomInstance()->description],
        );

        $response = $this->put(route('api.anime.update', ['anime' => $anime] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Anime Update Endpoint shall update an anime.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        $anime = Anime::factory()->createOne();

        $parameters = array_merge(
            Anime::factory()->raw(),
            [Anime::ATTRIBUTE_SEASON => AnimeSeason::getRandomInstance()->description],
        );

        Sanctum::actingAs(
            User::factory()->withCurrentTeam('editor')->createOne(),
            ['anime:update']
        );

        $response = $this->put(route('api.anime.update', ['anime' => $anime] + $parameters));

        $response->assertOk();
    }
}
