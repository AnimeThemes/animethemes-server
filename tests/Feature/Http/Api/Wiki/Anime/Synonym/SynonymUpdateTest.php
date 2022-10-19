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

        $response->assertUnauthorized();
    }

    /**
     * The Synonym Update Endpoint shall forbid users without the update anime synonym permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

        $parameters = AnimeSynonym::factory()->raw();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

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

        $user = User::factory()->withPermission('update anime synonym')->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.animesynonym.update', ['animesynonym' => $synonym] + $parameters));

        $response->assertOk();
    }
}
