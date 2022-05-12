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

        $response->assertUnauthorized();
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

        $user = User::factory()->withPermission('restore anime synonym')->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.animesynonym.restore', ['animesynonym' => $synonym]));

        $response->assertOk();
        static::assertNotSoftDeleted($synonym);
    }
}
