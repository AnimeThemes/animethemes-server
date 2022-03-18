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
 * Class SynonymRestoreTest.
 */
class SynonymRestoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Synonym Restore Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

        $synonym->delete();

        $response = $this->patch(route('api.animesynonym.restore', ['animesynonym' => $synonym]));

        $response->assertForbidden();
    }

    /**
     * The Synonym Restore Endpoint shall restore the synonym.
     *
     * @return void
     */
    public function testRestored(): void
    {
        $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

        $synonym->delete();

        Sanctum::actingAs(
            User::factory()->withCurrentTeam('editor')->createOne(),
            ['synonym:restore']
        );

        $response = $this->patch(route('api.animesynonym.restore', ['animesynonym' => $synonym]));

        $response->assertOk();
        static::assertNotSoftDeleted($synonym);
    }
}