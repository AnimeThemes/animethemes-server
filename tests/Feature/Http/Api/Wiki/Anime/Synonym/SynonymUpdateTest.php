<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime\Synonym;

use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class SynonymUpdateTest.
 */
class SynonymUpdateTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Synonym Update Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

        $parameters = AnimeSynonym::factory()->raw();

        $response = $this->put(route('api.animesynonym.update', ['animesynonym' => $synonym] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Synonym Update Endpoint shall update a synonym.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

        $parameters = AnimeSynonym::factory()->raw();

        Sanctum::actingAs(
            User::factory()->withCurrentTeam('editor')->createOne(),
            ['synonym:update']
        );

        $response = $this->put(route('api.animesynonym.update', ['animesynonym' => $synonym] + $parameters));

        $response->assertOk();
    }
}
