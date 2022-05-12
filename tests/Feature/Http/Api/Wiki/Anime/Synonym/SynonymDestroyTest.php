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
 * Class SynonymDestroyTest.
 */
class SynonymDestroyTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Synonym Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

        $response = $this->delete(route('api.animesynonym.destroy', ['animesynonym' => $synonym]));

        $response->assertUnauthorized();
    }

    /**
     * The Synonym Destroy Endpoint shall delete the synonym.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

        $user = User::factory()->createOne();

        $user->givePermissionTo('delete anime synonym');

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.animesynonym.destroy', ['animesynonym' => $synonym]));

        $response->assertOk();
        static::assertSoftDeleted($synonym);
    }
}
