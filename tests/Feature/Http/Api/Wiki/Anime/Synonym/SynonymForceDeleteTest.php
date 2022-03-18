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
 * Class SynonymForceDeleteTest.
 */
class SynonymForceDeleteTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Synonym Force Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

        $response = $this->delete(route('api.animesynonym.forceDelete', ['animesynonym' => $synonym]));

        $response->assertForbidden();
    }

    /**
     * The Synonym Force Destroy Endpoint shall force delete the synonym.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

        Sanctum::actingAs(
            User::factory()->withCurrentTeam('admin')->createOne(),
            ['*']
        );

        $response = $this->delete(route('api.animesynonym.forceDelete', ['animesynonym' => $synonym]));

        $response->assertOk();
        static::assertModelMissing($synonym);
    }
}
